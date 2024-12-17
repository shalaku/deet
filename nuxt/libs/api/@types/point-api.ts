export interface Point {
    id: number; // bigint(20) unsigned
    user_id: number; // bigint(20) unsigned
    order_id?: number; // bigint(20) unsigned | null
    point_amount: number; // int(11)
    pay_amount?: number; // int(11) | null
    payment_status?: string; // varchar(64) | null
    sell_date_time?: Date; // datetime | null
    created_at?: Date; // timestamp | null
    updated_at?: Date; // timestamp | null
}