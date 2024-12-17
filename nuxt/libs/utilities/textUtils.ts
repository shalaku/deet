export function linkify(text) {
    const urlPattern = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlPattern, '<a href="$&" target="_blank">$&</a>');
}