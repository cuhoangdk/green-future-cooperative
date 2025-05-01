export function getYouTubeVideoID(url: string): string | null {
  const regex = /(?:youtu\.be\/|youtube\.com\/(?:.*v=|embed\/|v\/|shorts\/))([\w-]{11})/;
  const match = url.match(regex);
  return match ? match[1] : null;
}

export function formatNumber(value?: number, locale: string = 'vi-VN', minimumFractionDigits: number = 0, maximumFractionDigits: number = 2): string {
  if (value === undefined || value === null) {
    return '';
  }
  return new Intl.NumberFormat(locale, {
    minimumFractionDigits,
    maximumFractionDigits
  }).format(value);
}

export function formatCurrency(value?: number, locale: string = 'vi-VN', currency: string = 'VND'): string {
  if (value === undefined || value === null) {
    return '';
  }
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency
  }).format(value);
}

export function formatDate(dateString?: string): string {
  if (!dateString) {
    return '';
  }
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
}

export function formatDateTime(dateString?: string): string {
  if (!dateString) {
    return '';
  }
  const date = new Date(dateString);
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${hours}:${minutes} ${day}/${month}/${year}`;
}

export function formatJson(data: string | null): string {
  try {
    return JSON.stringify(JSON.parse(data || '{}'), null, 2);
  } catch {
    return data || '-';
  }
}

export function formatStatus(status: string): string {
  switch (status) {
    case 'pending': {
      return 'Đang chờ xác nhận';
    }
    case 'processing': {
      return 'Đang xử lý';
    }
    case 'cancelled': {
      return 'Đã hủy';
    }
    case 'delivering': {
      return 'Đang giao';
    }
    case 'delivered': {
      return 'Đã giao';
    }
    default: {
      return 'Trạng thái không xác định';
    }
  }
}