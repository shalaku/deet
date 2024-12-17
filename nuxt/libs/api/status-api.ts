import { config } from '../config';
import {
	type HttpService,
	authService,
	HttpAuthService,
} from '../utilities/services';
import type { Status, StatusResponse } from './@types';

class StatusAPI {
	constructor(private readonly httpAuthService: HttpService) {}

	getAll() {
		return this.httpAuthService.get<Status[]>('statuses');
	}
	getStatusesByCategory(statusCategory: string) {
		const url = `statuses?status_category=${statusCategory}`;
		return this.httpAuthService.get<StatusResponse[]>(url);
	}
}

const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const statusAPI = new StatusAPI(httpAuthService);
