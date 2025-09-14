import axios, { AxiosInstance, AxiosResponse } from 'axios';
import { ApiResponse, AnalyticsData, Item, Category, Location } from '../types';

// Create axios instance with base configuration
const api: AxiosInstance = axios.create({
  baseURL: process.env.REACT_APP_API_URL || 'http://localhost:8080/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true, // Include cookies in requests
});

// Request interceptor to add auth token if available
api.interceptors.request.use(
  (config) => {
    // Add any authentication token here if needed
    const token = localStorage.getItem('authToken');
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
api.interceptors.response.use(
  (response: AxiosResponse) => {
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem('authToken');
      window.location.href = '/signin.php';
    }
    return Promise.reject(error);
  }
);

// API service object
export const analyticsAPI = {
  // Get analytics data
  getAnalytics: async (): Promise<AnalyticsData> => {
    try {
      const response = await api.get<ApiResponse<AnalyticsData>>('/analytics.php');
      return response.data.data || (response.data as unknown as AnalyticsData);
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch analytics data');
    }
  },

  // Get items data
  getItems: async (): Promise<Item[]> => {
    try {
      const response = await api.get<ApiResponse<Item[]>>('/items');
      return response.data.data || (response.data as unknown as Item[]);
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch items');
    }
  },

  // Get categories data
  getCategories: async (): Promise<Category[]> => {
    try {
      const response = await api.get<ApiResponse<Category[]>>('/categories');
      return response.data.data || (response.data as unknown as Category[]);
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch categories');
    }
  },

  // Get locations data
  getLocations: async (): Promise<Location[]> => {
    try {
      const response = await api.get<ApiResponse<Location[]>>('/locations');
      return response.data.data || (response.data as unknown as Location[]);
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch locations');
    }
  }
};

export default api;
