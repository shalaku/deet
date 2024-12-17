export interface OrderDetail {
    id: number;
    order_id: number;
    cast_id: number;
    order_acception_status: number;
    start_date_time: string;
    end_date_time: string;
    applied_point: number;
  }
  
  export interface Order {
    id: number;
    customer_id: number;
    order_type: string;
    status: number;
    planned_meeting_date_time: string;
    planned_meeting_time: string;
    // place_post_code: string;
    place_prefecture: string;
    place_municipalitie: string;
    place_street: string;
    place_building: string;
    place_desc: string;
    start_date_time: string;
    end_date_time: string;
    details: OrderDetail[];
  }
  
  export interface OrderResponse {
    status: string;
    status_code: number;
    message: string;
    data: {
      orders: Order[];
      total: number;
      pages: number;
    };
  }