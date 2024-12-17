import { config } from '../config';
import {
	type HttpService,
	authService,
	HttpAuthService,
} from '../utilities/services';

import type {
	Cast,
	Customer,
	CustomerResponse,
	StripeCreatePaymentIntentPayload,
	StripeCreatePaymentMethodPayload,
	stripeCreateSubscription,
	SubscriptionResponse,
} from './@types';

class CustomerAPI {
	constructor(private readonly httpAuthService: HttpService) {}

	getAll(params: Record<string, any> = {}) {
		const filteredParams = Object.fromEntries(
			Object.entries(params).filter(
				([_, value]) => value !== null && value !== '',
			),
		);

		const queryString = new URLSearchParams(filteredParams).toString();
		const url = `customers?limit=20&offset=0&${queryString}`;
		return this.httpAuthService.get<Customer[]>(url);
	}
	getCustomerById(id: number) {
		return this.httpAuthService.get<CustomerResponse>(`customers/${id}`);
	}

	getCast(params: Record<string, any> = {}) {
		const { limit = 10, page = 0, ...otherParams } = params;
		//const offset = page * limit;
		const formattedParams = { limit, page, ...otherParams };
		const filteredParams = Object.fromEntries(
			Object.entries(formattedParams).filter(
				([_, value]) => value !== null && value !== '',
			),
		);
		const queryString = new URLSearchParams();
		for (const [key, value] of Object.entries(filteredParams)) {
			if (Array.isArray(value)) {
				value.forEach((val) => queryString.append(key, val));
			} else {
				queryString.append(key, value);
			}
		}
		const url = `cast?${queryString.toString()}`;
		return this.httpAuthService.get<Cast[]>(url);
	}

	getCustomerProfile() {
		return this.httpAuthService.get('customers/profile');
	}
	customerUpdate(id: number, updatedFields: object): Promise<any> {
		return this.httpAuthService.put(`customers/${id}`, updatedFields);
	}
	addCustomer(customer: object) {
		return this.httpAuthService.post('customers', customer);
	}

	deleteCustomer(id: number) {
		const url = `customers/${id}`;
		return this.httpAuthService.delete(url);
	}

	makeOrder(orderList: object) {
		console.log('bookingDetails__', orderList);
		return this.httpAuthService.post('orders/direct-call-cast', orderList);
	}
	requestMatching(matchingList: object) {
		return this.httpAuthService.post('request-matching/create', matchingList);
	}

	uploadImage(id: number, file: File, type: string) {
		const url = '/image/store'; // Adjust URL based on your API
		const formData = new FormData();

		// Append the image file
		formData.append(`images[0][image]`, file);

		// Append additional data
		formData.append(`images[0][image_for]`, type);
		formData.append(`images[0][owner_id]`, id.toString());

		return this.httpAuthService.post(url, formData);
	}

	getFavCasts() {
		return this.httpAuthService.get('customer/get-fav-casts?limit=20&page=1');
	}

	removeFavCast(castId: number) {
		return this.httpAuthService.post('customer/remove-fav-cast', {
			cast_id: castId,
		});
	}

	addFavCast(castId: number) {
		return this.httpAuthService.post('customer/add-fav-cast', {
			cast_id: castId,
		});
	}
	updateUserNameChat(customerId: number, nickname: string) {
		const body = {
			id: customerId,
			nickname: nickname,
		};
		return this.httpAuthService.post('tencent/chat/update-user', body);
	}

	updateAvatarChat(customerId: number, avatar: string) {
		return this.httpAuthService.post('tencent/chat/update-user', {
			id: customerId,
			avatar,
		});
	}

	pointList() {
		const url = 'payments/amount';
		return this.httpAuthService.get<{ id: number; points: number }[]>(url);
	}

	createPaymentIntent(payload: StripeCreatePaymentIntentPayload) {
		return this.httpAuthService.post<{ clientSecret: string }>(
			'payments/create',
			payload,
		);
	}

	savePaymentMethodForCard(payload: StripeCreatePaymentMethodPayload) {
		return this.httpAuthService.post<{ success: string; message: string }>(
			'/cards/save',
			payload,
		);
	}

	getCustomerCardInfo(id: number) {
		const url = `cards/${id}`;
		return this.httpAuthService.get<{}>(url);
	}

	deleteCustomerCard(paymentMethodId: number, userId: number) {
		const url = `cards/delete/${userId}/${paymentMethodId}`;
		return this.httpAuthService.delete<{ success: string; message: string }>(
			url,
		);
	}
	getSubscriptionList() {
		const url = 'subscriptions/stripe-subscription-products';
		return this.httpAuthService.get<SubscriptionResponse>(url);
	}

	createSubscription(payload: stripeCreateSubscription){
		return this.httpAuthService.post<{ success: string; message: string }>(
			'subscriptions/create',
			payload,
		);
	}
}
const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const customerAPI = new CustomerAPI(httpAuthService);
