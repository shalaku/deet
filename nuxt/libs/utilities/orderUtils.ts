const TYPES = {
    'request': 'コール',
    'direct': 'Deet',
  };


  export const getOrderType = (type: string) => {
    return TYPES[type] || '不明';
  };
