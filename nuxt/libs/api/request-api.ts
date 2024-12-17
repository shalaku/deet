/* eslint-disable @typescript-eslint/no-explicit-any */
import { config } from '../config';
import {
  type HttpService,
  authService,
  HttpAuthService
} from '../utilities/services';
import type { RequestResponse } from './@types';

class RequestAPI {
  constructor(private readonly httpAuthService: HttpService) {}

  getRequestsByStatus(status: number) {
    const url = `request-matching?status=${status}`;
    return this.httpAuthService.get<RequestResponse>(url);
  }

  getRequestsByStatusPagar(status: number,currentpage:number) {
    const url = `request-matching?limit=10&page=${currentpage}&status=${status}`;
    return this.httpAuthService.get<RequestResponse>(url);
  }
  
  getRequestsBy501and504(status: number,currentpage:number) {
    const url = `request-matching?limit=10&page=${currentpage}&501and504=1`;
    return this.httpAuthService.get<RequestResponse>(url);
  }
  
  getDesignatedRequest() {
    const url = `request-matching-designated`;
    return this.httpAuthService.get<RequestResponse>(url);
  }

  getRequestsFilteredCastRank() {
    const url = `request-matching?cast_rank_filter=1`;
    return this.httpAuthService.get<RequestResponse>(url);
  }
  
  getAllRequests(params: Record<string, any> = {}) {
    const filteredParams = Object.fromEntries(
			Object.entries(params).filter(
				([_, value]) => value !== null && value !== '',
			),
		);

		const queryString = new URLSearchParams(filteredParams).toString();
		const url = `request-matching?limit=100&offset=0&${queryString}`;
		return this.httpAuthService.get<RequestResponse[]>(url);
  }

  createDetail(data: Record<string, any>) {
    const url = 'request-matching/detail';
    return this.httpAuthService.post(url, data);
  }

  fetchCastData(params: Record<string, any> = {}) {
    // Convert the parameters to a query string
    const queryString = new URLSearchParams(params).toString();
  
    // Construct the API URL
    const url = `cast?${queryString}`;
  
    // Make the HTTP request
    return this.httpAuthService.get<RequestResponse[]>(url);
  }

  requestCancel(requestId: number) {
    const url = 'request-matching/detail/cancel';
    const data = {
      request_matching_id: requestId
    };
  
    return this.httpAuthService.post(url, data);

  }
  castConfirm(requestData:object)  {
    const url = "request-matching/detail/cast-confirm";
    return this.httpAuthService.post(url, requestData);
  };

  forceclosewithsuccess(requestId: number) {
    const url = 'request-matching/detail/confirm';
    const data = {
      request_matching_id: requestId
    };
  
    return this.httpAuthService.post(url, data);

  }

 getPushNotification(data:object){
  const url = '/notifications/subscribe';
  return this.httpAuthService.post(url, data);
 }
 getPushNotificationunsubscribe(data:object){
  const url = '/notifications/unsubscribe';
  return this.httpAuthService.post(url, data);
 }
  
}

const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const requestAPI = new RequestAPI(httpAuthService);