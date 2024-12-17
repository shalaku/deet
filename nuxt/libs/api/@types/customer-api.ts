export interface Customer {
	id: number;
	name_ja: string;
	nickname: string;
	introducer_id: any;
	person_in_charge_id: number;
	registered_date: string;
	status_id: number;
	birthday: string;
	category_id: number;
	tag_of_spec: string[];
	created_at: string;
	updated_at: string;
}

export interface CustomerData {
	id: number;
	status_id: number;
	name_ja: string;
	name_kana: string;
	nickname: string;
	birthday: string;
	introducer_id: number;
	person_in_charge_id: number;
	category_id: number;
	registered_date: string;
	tag_of_preference: string[];
	notices: string;
	height: number | null;
	municipalitie: string | null;
	street: string | null;
	building: string | null;
	type: string | null;
	work: string | null;
	hair: string | null;
	hair_style: string | null;
	alcohol: string | null;
	education: string | null;
	tobacco: string | null;
	siblings: number | null;
	cohabitant: number | null;
	fav_cast_ids: number[] | null;
	points_holded: number;
	images: any[];
}

export interface CustomerResponse {
	status: string;
	status_code: number;
	message: string;
	data: CustomerData;
}

export interface StripeCreatePaymentIntentPayload {
	points: number;
	user_id: number;
	user_type: string;
	currency: string;
	email: string;
}

export interface StripeCreatePaymentMethodPayload {
	paymentMethodId:string;
	userId:number;
}

export interface Price {
	price_id: string;
	currency: string;
	unit_amount: string;
	interval: string;
	interval_count: number;
  }
  
  export interface Subscription {
	id: string;
	name: string;
	description: string;
	active: boolean;
	prices: Price[];
  }
  
  export interface SubscriptionResponse {
	status: string;
	status_code: number;
	message: string;
	data: Subscription[];
  }

  export interface stripeCreateSubscription {
	user_id:number;
	plan_id:string;
	stripe_customer_id:string;
	points:number;
	email:string;
	currency:string;
	payment_for:string;
  }
