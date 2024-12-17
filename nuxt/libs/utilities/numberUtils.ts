export const formatNumber = (value) => {
    if (value === null || value === undefined) return '';
    // 小数点以下を削除し、カンマを付与
    return parseInt(value).toLocaleString();
};

export const convertToHalfWidth = (input) => {
    // 入力が文字列でない場合は文字列に変換
    if (typeof input !== 'string') {
        input = String(input);
    }

    // 全角数字を半角数字に変換し、数値以外を削除
    const result = input
        .replace(/[０-９]/g, (char) => String.fromCharCode(char.charCodeAt(0) - 0xFEE0)) // 全角から半角に変換
        .replace(/[^0-9]/g, ''); // 数値以外を削除

    // 結果が空の場合は0を返す
    return result === '' ? '0' : result;
};
