export interface RequestDetail {
    id: number;
    matching_id: number;
    cast_id: number;
    status: number;
  }
  
  export interface Request {
    id: number;
    customer_id: number;
    status: number;
    municipalities: string;
    area_name: string;
    numberof_people: number;
    requested_start_time: string;
    requested_matching_term: number;
    cast_age_min: number;
    cast_age_max: number;
    cast_height_min: number;
    cast_height_max: number;
    cast_tag: string;
    cast_rank: string;
    mid_night_fee: string;
    details: RequestDetail[];
  }
  
  export interface RequestResponse {
    status: string;
    status_code: number;
    message: string;
    data: {
      requests: Request[];
      total: number;
      pages: number;
    };
  }

  export interface RequestMatchingUpdatePayload {
    request_id: number,
    cast_id: number,
  }