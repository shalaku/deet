export const calculateAge = (dob: Date): number => {
    if (!(dob instanceof Date) || isNaN(dob.getTime())) {
        console.error('Invalid date provided to calculateAge:', dob);
        return 0; // or handle the error as needed
      }
    const diff = Date.now() - dob.getTime();
    const ageDate = new Date(diff);
    return Math.abs(ageDate.getUTCFullYear() - 1970);
  };


export const formatDateTimeWithYear = (dateTime: string): string => {
    // Convert "YYYY-MM-DD HH:MM:SS" to "YYYY-MM-DDTHH:MM:SS" for ISO compatibility
    const date = new Date(dateTime.replace(" ", "T"));

    // Extract day, month, year, hour, and minute
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const year = String(date.getFullYear()).slice(-2);          // Last 2 digits of the year
    const hour = String(date.getHours()).padStart(2, '0');
    const minute = String(date.getMinutes()).padStart(2, '0');

    // Format as "DD/MM/YY HH:MM"
    return `${year}/${month}/${day} ${hour}:${minute}`;
};

export const formatDateTimeJp = (dateTime: string): string => {
  // Convert "YYYY-MM-DD HH:MM:SS" to "YYYY-MM-DDTHH:MM:SS" for ISO compatibility
  const date = new Date(dateTime.replace(" ", "T"));

  // Extract day, month, year, hour, and minute
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
  const year = String(date.getFullYear()).slice(-2);          // Last 2 digits of the year
  const hour = String(date.getHours()).padStart(2, '0');
  const minute = String(date.getMinutes()).padStart(2, '0');

  // Format as "DD/MM/YY HH:MM"
  return `${month}/${day} ${hour}:${minute}`;
};

export const formatDateJpShort = (dateString: string | null) => {
  if (!dateString) {
    return '---';
  }

  const date = new Date(dateString);
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const daysOfWeek = ['日', '月', '火', '水', '木', '金', '土'];
  const dayOfWeek = daysOfWeek[date.getDay()];
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');

  return `${month}/${day}（${dayOfWeek}） ${hours}:${minutes}`;
};

export const formatMeetingTime=(timeString: string): string =>{
  // Split the time string into hours, minutes, and seconds
  const [hour, minute, second] = timeString.split(":");

  // If minutes are "00", return only the hour
  if (minute === "00") {
    return `${Number(hour)}時間`;
  }
  // Otherwise, return both hour and minute
  return `${Number(hour)}時間${Number(minute)}分`;
}

export const formatDateTimeToDate = (dateTimeString: string): string => {
  // "YYYY-MM-DD HH:MM:SS"形式の日付文字列を受け取る
  const dateParts = dateTimeString.split(" ");
  return dateParts[0]; // 日付部分を返す
};