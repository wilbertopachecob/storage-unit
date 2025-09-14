// API Response Types
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
}

// Analytics Types
export interface AnalyticsData {
  total_items: number;
  total_quantity: number;
  items_by_category: CategoryAnalytics[];
  items_by_location: LocationAnalytics[];
  recent_items: Item[];
  monthly_data: Record<string, number>;
  items_without_images: number;
  items_with_images: number;
  image_coverage: number;
  avg_quantity: number;
}

export interface CategoryAnalytics {
  name: string;
  color: string;
  count: number;
}

export interface LocationAnalytics {
  name: string;
  count: number;
}

// Item Types
export interface Item {
  id: number;
  title: string;
  description: string;
  qty: number;
  img?: string;
  category_id?: number;
  location_id?: number;
  category_name?: string;
  category_color?: string;
  location_name?: string;
  created_at: string;
  updated_at: string;
}

export interface Category {
  id: number;
  name: string;
  color: string;
  icon: string;
  user_id: number;
  created_at: string;
  updated_at: string;
}

export interface Location {
  id: number;
  name: string;
  parent_id?: number;
  user_id: number;
  created_at: string;
  updated_at: string;
}

// Component Props Types
export interface MetricCardProps {
  title: string;
  value: number;
  icon: string;
  color?: 'primary' | 'success' | 'info' | 'warning';
}

export interface QuickStatsProps {
  analytics: AnalyticsData;
}

export interface RecentItemsProps {
  items: Item[];
}

// Chart Data Types
export interface ChartData {
  labels: string[];
  datasets: ChartDataset[];
}

export interface ChartDataset {
  data: number[];
  backgroundColor?: string | string[];
  borderColor?: string | string[];
  borderWidth?: number;
  label?: string;
  tension?: number;
  fill?: boolean;
}

// API Service Types
export interface ApiService {
  getAnalytics: () => Promise<AnalyticsData>;
  getItems: () => Promise<Item[]>;
  getCategories: () => Promise<Category[]>;
  getLocations: () => Promise<Location[]>;
}

// Error Types
export interface ApiError {
  message: string;
  status?: number;
  code?: string;
}
