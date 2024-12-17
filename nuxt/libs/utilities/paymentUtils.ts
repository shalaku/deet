const TYPES = {
    'Initiated': '処理中',
    'Success': '成功',
  };


  export const getStripeStatus = (type: string) => {
    return TYPES[type] || '不明';
  };
