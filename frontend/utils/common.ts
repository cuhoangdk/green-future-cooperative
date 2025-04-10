export function getYouTubeVideoID(url: string): string | null {
    const regex = /(?:youtu\.be\/|youtube\.com\/(?:.*v=|embed\/|v\/|shorts\/))([\w-]{11})/;
    const match = url.match(regex);
    return match ? match[1] : null;
  }

  export function formatNumber(value: number, locale: string = 'vi-VN', minimumFractionDigits: number = 0, maximumFractionDigits: number = 2): string {
    return new Intl.NumberFormat(locale, {
      minimumFractionDigits,
      maximumFractionDigits
    }).format(value);
  }

  export function formatCurrency(value: number, locale: string = 'vi-VN', currency: string = 'VND'): string {
    return new Intl.NumberFormat(locale, {
      style: 'currency',
      currency
    }).format(value);
  }

  export function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const day = String(date.getUTCDate()).padStart(2, '0');
    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
    const year = date.getUTCFullYear();
    return `${day}/${month}/${year}`;
  }

  export function formatDateTime(dateString: string): string {
    const date = new Date(dateString);
    const minutes = String(date.getUTCMinutes()).padStart(2, '0');
    const hours = String(date.getUTCHours()).padStart(2, '0');
    const day = String(date.getUTCDate()).padStart(2, '0');
    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
    const year = date.getUTCFullYear();
    return `${hours}:${minutes} ${day}/${month}/${year}`;
  }

