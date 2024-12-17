import { config } from '../config';
import {
	type HttpService,
	authService,
	HttpAuthService,
} from '../utilities/services';
import type { Point } from './@types';

class PointAPI {
	constructor(private readonly httpAuthService: HttpService) {}

	getAll(params: Record<string, any> = {}) {
		const filteredParams = Object.fromEntries(
			Object.entries(params).filter(
				([_, value]) => value !== null && value !== '',
			),
		);

		const queryString = new URLSearchParams(filteredParams).toString();
		const url = `point-history?limit=100&offset=0&${queryString}`;
		return this.httpAuthService.get<Point[]>(url);

	}

	createWithHoldedPoint(data: Record<string, any>) {
		const url = 'point-history/create-with-holded-point';
		return this.httpAuthService.post(url, data);
	}
}

const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const pointAPI = new PointAPI(httpAuthService);
