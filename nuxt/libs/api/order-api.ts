/* eslint-disable @typescript-eslint/no-explicit-any */
import { config } from '../config';
import {
  type HttpService,
  authService,
  HttpAuthService,
} from '../utilities/services';
import type { OrderResponse } from './@types';

class OrderAPI {
  constructor(private readonly httpAuthService: HttpService) {}

  getOrdersByStatus(status: number) {
    const url = `orders?status=${status}`;
    return this.httpAuthService.get<OrderResponse>(url);
  }

  getProgressOrdersInCastSide() {
    const url = `orders?search=progress`;
    return this.httpAuthService.get<OrderResponse>(url);
  }
  
  getConfirmedOrdersInCastSide() {
    const url = `orders?search=confirmed`;
    return this.httpAuthService.get<OrderResponse>(url);
  }

  
  getOrdersByStatusPagar(status: number,currentpage:number) {
    const url = `orders?limit=10&page=${currentpage}&status=${status}`;
    return this.httpAuthService.get<OrderResponse>(url);
  }
  getHistoryOrders() {
    const url = 'orders?history=true';
    return this.httpAuthService.get<OrderResponse>(url);
  }  
  getAllOrders(params: Record<string, any> = {}) {
    const filteredParams = Object.fromEntries(
			Object.entries(params).filter(
				([_, value]) => value !== null && value !== '',
			),
		);

		const queryString = new URLSearchParams(filteredParams).toString();
		const url = `orders?limit=100&offset=0&${queryString}`;
		return this.httpAuthService.get<OrderResponse[]>(url);

    // const url = 'orders';
    // return this.httpAuthService.get<OrderResponse>(url);
  }
  
  getOrdersWithCustomer() {
    const url = 'orders/customer-test';
    return this.httpAuthService.get<OrderResponse>(url);    
  }

  saveAdminMemo(data: Record<string, any>) {
    const url = 'orders/save-admin-memo';
    return this.httpAuthService.post(url, data);
  }

  saveCastMemo(data: Record<string, any>) {
    const url = 'orders/save-cast-memo';
    return this.httpAuthService.post(url, data);
  }  

  leaveOrder(data: Record<string, any>) {
    const url = 'orders/leave-order';
    return this.httpAuthService.post(url, data);
  }
  
  startOrder(data: Record<string, any>) {
    const url = 'orders/start-order';
    return this.httpAuthService.post(url, data);
  }

  acceptDeet(data: Record<string, any>) {
    const url = 'orders/accept-deet';
    return this.httpAuthService.post(url, data);
  }

  rejectDeet(data: Record<string, any>) {
    const url = 'orders/reject-deet';
    return this.httpAuthService.post(url, data);
  }
}

const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const orderAPI = new OrderAPI(httpAuthService);