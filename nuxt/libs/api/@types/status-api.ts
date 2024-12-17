export interface Status {
	id: number;
	status_category: string;
	status_name: string;
	desc: string;
	created_at: string;
	updated_at: string;
}
export interface StatusResponse {
	status: string;
	status_code: number;
	message: string;
	data: any;
}
