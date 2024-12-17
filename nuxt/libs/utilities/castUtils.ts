const ALCOHOL = {
  'good': "強い",
  'average': "普通",
  'bad': "弱い",
  'not': "NG",
};

const FOOTWORK = {
  'good': "軽い",
  'average': "普通",
  'bad': "重い",
};

const VIP = {
  'good': "推奨",
  'average': "可能",
  'not': "不可",
};

const BANKACCOUNTTYPE = {
  'normal': "普通",
  'special': "当座",
};

const RANKPOINT = {
  'BLACK': "10000",
  'PLATINUM': "8000",
  'GOLD': "6000",
};

const CUPSIZELIST = [
  { label: 'AAA', value: 'AAA' },
  { label: 'AA', value: 'AA' },
  { label: 'A', value: 'A' },
  { label: 'B', value: 'B' },
  { label: 'C', value: 'C' },
  { label: 'D', value: 'D' },
  { label: 'E', value: 'E' },
  { label: 'F', value: 'F' },
  { label: 'G', value: 'G' },
  { label: 'H', value: 'H' },
  { label: 'I', value: 'I' },
  { label: 'J', value: 'J' },
  { label: 'K', value: 'K' },
];

// Utility functions
const createListFromObject = (obj) => {
  return Object.entries(obj).map(([key, value]) => ({ label: value, value: key }));
};

export const getAlcoholTxt = (str) => {
  return ALCOHOL[str] || '不明';
};

export const getAlcoholList = () => {
  return createListFromObject(ALCOHOL);
};

export const getFootworkTxt = (str) => {
  return FOOTWORK[str] || '不明';
};

export const getFootworkList = () => {
  return createListFromObject(FOOTWORK);
};

export const getVipTxt = (str) => {
  return VIP[str] || '不明';
};

export const getVipList = () => {
  return createListFromObject(VIP);
};

export const getBankAccountTypeTxt = (str) => {
  return BANKACCOUNTTYPE[str] || '-';
};

export const getRankPointValue = (str) => {
  return RANKPOINT[str] || '-';
};

export const getRankList = () => {
  return Object.entries(RANKPOINT).map(([key, value]) => ({ label: key, value: key }));
};

export const getCupSizeList = () => {
  return CUPSIZELIST;
};