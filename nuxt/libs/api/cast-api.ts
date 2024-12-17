/* eslint-disable @typescript-eslint/no-explicit-any */
import { config } from '../config';
import {
	type HttpService,
	authService,
	HttpAuthService,
} from '../utilities/services';

import axios from 'axios';
import type {
	Cast,
	CastLandingPageResponse,
	CastProfileResponse,
	CastProfileUpdatePayload,
	UpdateCastFields,
} from './@types';

class CastAPI {
	constructor(private readonly httpAuthService: HttpService) {}

	profile() {
		return this.httpAuthService.get<CastProfileResponse>('cast/profile');
	}
	getLandingPage() {
		return this.httpAuthService.get<CastLandingPageResponse>('cast/landing/page');
	}
	aggregateOrdersMonthly() {
		return this.httpAuthService.get('cast/aggregate-orders-monthly');
	}

	updateProfile(data: Partial<CastProfileUpdatePayload>) {
		return this.httpAuthService.put('cast/update', data);
	}

	getAll(params: Record<string, any> = {}) {
		const filteredParams = Object.fromEntries(
			Object.entries(params).filter(
				([_, value]) => value !== null && value !== '',
			),
		);

		const queryString = new URLSearchParams(filteredParams).toString();
		const url = `cast?limit=10&offset=0&${queryString}`;
		return this.httpAuthService.get<Cast[]>(url);
	}

	create(data: Record<string, any>) {
		const url = 'cast';
		return this.httpAuthService.post(url, data);
	}
	deleteCast(id: number) {
        return this.httpAuthService.delete(`cast/${id}`);
    }

	getCastInfo(id: number) {
        return this.httpAuthService.get<CastProfileResponse>(`cast/${id}`);
    }

	castUpdate(id: number, updatedFields: UpdateCastFields): Promise<any> {
		return this.httpAuthService.put(`cast/${id}`, updatedFields);
	}
	uploadPhotos(images: File[], ownerId: string) {
		const url = 'image/store';
		const formData = new FormData();

		images.forEach((image, index) => {
			formData.append(`images[${index}][image]`, image);
			formData.append(`images[${index}][image_for]`, 'cast');
			formData.append(`images[${index}][owner_id]`, ownerId);
		});

		return this.httpAuthService.post(url, formData);
	}

	deletePhoto(id: number) {
		return this.httpAuthService.delete(`image/${id}`);
	}

	makeProfilePicture(ownerId: number, imageId: number) {
		const url = 'image/make-profile';
		return this.httpAuthService.post(url, {
			image_for: 'cast',
			owner_id: ownerId,
			image_id: imageId,
		});
	}

	getNewCasts({ page = 0, limit = 10 }) {
        return this.httpAuthService.get(`customer/home-page?new_cast=true&limit=${limit}&page=${page}`);
    }

	getAvailableCasts({ page = 0, limit = 10 }){
		return this.httpAuthService.get(`customer/home-page?available_cast=true&limit=${limit}&page=${page}`);
	}

	getNewCastsAge({ age, page = 0, limit = 10 }) {
		console.log(age)
        return httpAuthService.get(`customer/home-page?limit=${limit}&page=${page}&age=${age}`);
    }


	async fetchAddressByPostalCode(postalCode: string) {
		if (postalCode.length !== 7) return null;

		try {
			const response = await axios.get(
				`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postalCode}`,
			);
			const result = response.data.results?.[0];
			if (result) {
				return {
					prefecture: result.address1,
					city: result.address2,
					townName: result.address3,
				};
			}
			return null;
		} catch (error) {
			console.error('住所情報の取得に失敗しました:', error);
			throw error;
		}
	}
	updateUserNameChat(castId:number,nickname:string){
		const body = {
			id: castId,
			nickname: nickname
		  };
		return this.httpAuthService.post('tencent/chat/update-user', body);
	  }

	  updateAvatarChat(castId:number,avatar:string){
		const body = {
			id: castId,
			avatar: avatar
		  };
		return this.httpAuthService.post('tencent/chat/update-user', body);
	  }
}

const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const castAPI = new CastAPI(httpAuthService);
