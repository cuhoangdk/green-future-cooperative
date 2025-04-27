// types/statistics.ts
export interface OrderPerDay {
    count: number
    total_amount: number
  }
  
  export interface ProductByRevenue {
    id: number
    name: string
    product_unit: string
    total_revenue: string
  }
  
  export interface ProductByQuantity {
    id: string
    name: string
    product_unit: string
    total_quantity: string
  }
  
  export interface UserByRevenue {
    id: number
    full_name: string
    total_revenue: string
  }
  
  export interface CategoryInfo {
    id: number
    name: string
    product_count: number
    percentage: string
  }
  
  export interface PurchasedCategory {
    id: number
    name: string
    total_revenue: string
    percentage: number
  }
  
  export interface Statistics {
    start_date: string
    end_date: string
    new_customers: number
    new_users: number
    new_products: number
    products_by_status: {
      growing: number
      selling: number
      stopped: number
    }
    new_orders: number
    orders_by_status: {
      pending: number
      processing: number
      delivering: number
      delivered: number
      cancelled: number
    }
    orders_per_day: Record<string, OrderPerDay>
    total_revenue: number
    top_products_by_revenue: ProductByRevenue[]
    top_products_by_quantity: ProductByQuantity[]
    top_users_by_revenue: UserByRevenue[]
    top_customers_by_revenue: UserByRevenue[]
    products_by_category: CategoryInfo[]
    purchased_by_category: PurchasedCategory[]
  }