/* eslint-disable @typescript-eslint/no-explicit-any */
export interface CastProfileResponse {
	data: {
		cast: Cast;
		bank_details: CastBankDetail;
		images: CastImage[];
		compatible_areas: string[];
	};
}
export interface CastLandingPageResponse {
	profile_data: {
		cast: Cast;
		bank_details: CastBankDetail;
		images: CastImage[];
		compatible_areas: string[];
	};
}

export interface Cast {
	id: number;
	user_id: number;
	name_ja: string;
	name_kana: string;
	nickname: string;
	city: string;
	introducer_id: any;
	person_in_charge_id: number;
	registered_date: string;
	status: number;
	rank: string;
	post_code: string;
	prefecture: string;
	municipalitie: string;
	street: string;
	building: string;
	station: string;
	footwork: string;
	alcohol: string;
	day_work: string;
	night_work: string;
	height: number;
	three_size_b: number;
	three_size_w: number;
	three_size_h: number;
	vip_status: string;
	birthday: string;
	ceo_check: number;
	instagram_id: string;
	category_id: number;
	tag_of_spec: string[];
	notices: string;
	prefecture_and_municipality: any;
	smoke: any;
	my_comment: any;
	siblings: number;
	cohabitation: number;
	created_at: string;
	updated_at: string;
	hair_color: string;
	hair_style: string;
	final_academic_background: string;
	basic_point_price: number;
}

export interface CastImage {
	id: number;
	title: any;
	image_url: string;
	file_type: any;
	is_profile_picture: number;
}

export interface CastBankDetail {
	bank_name: string;
	branch: string;
	account_number: string;
	account_type: string;
}

export type CastProfileUpdatePayload = Partial<Cast> & {
	bank_details?: CastBankDetail;
};

export type UpdateCastFields = {
	name_ja?: string;
	name_kana?: string;
	nickname?: string;
	city?: string;
	person_in_charge_id?: string;
	registered_date?: string;
	status_id?: string;
	rank?: string;
	post_code?: string;
	prefecture?: string;
	municipalitie?: string;
	street?: string;
	station?: string;
	footwork?: string;
	alcohol?: string;
	day_work?: string;
	night_work?: string;
	height?: string;
	three_size_b?: string;
	three_size_w?: string;
	three_size_h?: string;
	vip_status?: string;
	birthday?: string;
	ceo_check?: boolean;
	instagram_id?: string;
	introducer?: string;
	workStatus?: string;
	point_rate?: number;
  };
   
