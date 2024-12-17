import { config } from '../config';
import {
	type HttpService,
	authService,
	HttpAuthService,
} from '../utilities/services';
import type { Category } from './@types';

class CategoryAPI {
	constructor(private readonly httpAuthService: HttpService) {}

	getAll() {
		return this.httpAuthService.get<Category[]>('cast-categories');
	}
	getCustomerAll() {
		return this.httpAuthService.get<Category[]>('customer-categories');
	}
	getCastCategoryName(id: number) {
        return this.httpAuthService.get(`cast-categories/${id}`);
    }
	getCustomerCategoryName(id: number) {
        return this.httpAuthService.get(`customer-categories/${id}`);
    }

}

const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const categoryAPI = new CategoryAPI(httpAuthService);
