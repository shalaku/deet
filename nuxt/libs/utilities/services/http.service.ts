import { config as appConfig } from '../../config';

import type {
	HttpServiceConfig,
	HttpServiceHeaders,
	HttpServiceResponse,
} from './types';

export class HttpService {
	constructor(
		private readonly baseURL: string,
		private readonly config: HttpServiceConfig = {},
	) {}

	get<T>(url: string, headers?: HttpServiceHeaders) {
		return this.request<T>('GET', url, null, headers);
	}

	post<T>(url: string, payload: unknown, headers?: HttpServiceHeaders) {
		return this.request<T>('POST', url, payload, headers);
	}

	put<T>(url: string, payload: unknown, headers?: HttpServiceHeaders) {
		return this.request<T>('PUT', url, payload, headers);
	}

	delete<T>(url: string, headers?: HttpServiceHeaders) {
		return this.request<T>('DELETE', url, null, headers);
	}

	private async request<T>(
		method: string,
		url: string,
		payload?: unknown,
		headers?: HttpServiceHeaders,
	): Promise<HttpServiceResponse<T>> {
		const requestURL = `${this.baseURL}/${url}`;
		const isFormData = payload instanceof FormData;

		// Attach extra headers to request header
		const requestInit: RequestInit = {
			method,
			headers: {
				'Content-Type': 'application/json',
				...headers,
			},
		};

		if (isFormData) {
			delete (requestInit.headers as Record<string, string>)['Content-Type'];
		}

		// Attach token to request header
		if (this.config.getToken && typeof this.config.getToken === 'function') {
			const accessToken = await this.config.getToken();

			if (accessToken) {
				requestInit.headers = {
					...requestInit.headers,
					Authorization: `Bearer ${accessToken}`,
				};
			}
		}

		// Attach payload to request body
		if (['POST', 'PUT', 'PATCH'].includes(method)) {
			requestInit.body = isFormData ? payload : JSON.stringify(payload);
		}

		try {
			const response = await fetch(requestURL, requestInit);

			// If token is expired, retrieve new token and retry request
			// if (response.status === 401) {
			// 	await this.retrieveToken();
			// 	return this.request<T>(method, url, payload, headers);
			// }

			// Parse result based on content type
			const result = await response.json();

			if (!response.ok) {
				// console.log(result);
				// alert(JSON.stringify(result.errors,null,2));
				if (result) {
					throw new Error(JSON.stringify(result.errors, null, 2));
				} else {
					throw new Error('Something went wrong');
				}
				// throw new Error(result?JSON.stringify(result.errors,null,2) || 'Something went wrong');
				// throw new Error(result?.message || 'Something went wrong');
			}

			return result;
		} catch (error) {
			if (error instanceof Error) {
				throw new Error(error.message);
			}

			throw new Error('Something went wrong');
		}
	}

	private async retrieveToken() {
		const refreshToken = await this.config.getRefreshToken?.();

		try {
			if (!refreshToken) {
				throw new Error('Unauthorised');
			}

			const requestURL = `${appConfig.apiBaseURL}/refresh`;
			const response = await fetch(requestURL, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: `Bearer ${refreshToken}`,
				},
			});

			const result = await response.json();

			if (!response.ok) {
				throw new Error(result?.message || 'Unauthorised');
			}

			const {
				data: { access_token, refresh_token },
			} = result;

			this.config.onRetrieveTokens?.({
				token: access_token,
				refreshToken: refresh_token,
			});

			return result;
		} catch (error) {
			this.config.onUnauthorised?.();

			if (error instanceof Error) {
				throw new Error(error.message);
			}

			throw new Error('Unauthorised');
		}
	}
}
