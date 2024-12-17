export interface RetrieveTokensPayload {
	token: string;
	refreshToken: string;
}

export interface HttpServiceConfig {
	getToken?: () => string | null | Promise<string | null>;
	getRefreshToken?: () => string | null | Promise<string | null>;
	onRetrieveTokens?: (payload: RetrieveTokensPayload) => void;
	onUnauthorised?: () => void;
}

export type HttpServiceHeaders = Record<string, string>;

export interface HttpServiceResponse<T> {
	status: 'SUCCESS' | 'FAILED';
	status_code: number;
	message: string;
	data: T;
}
