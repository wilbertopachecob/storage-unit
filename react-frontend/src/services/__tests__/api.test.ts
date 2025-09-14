import axios from 'axios';
import { analyticsAPI } from '@/services/api';
import { AnalyticsData, Item, Category, Location } from '@/types';

// Mock axios
jest.mock('axios');
const mockedAxios = axios as jest.Mocked<typeof axios>;

describe('API Service', () => {
  const mockAxiosInstance = {
    get: jest.fn(),
    post: jest.fn(),
    put: jest.fn(),
    delete: jest.fn(),
    interceptors: {
      request: { use: jest.fn() },
      response: { use: jest.fn() },
    },
  };

  beforeEach(() => {
    jest.clearAllMocks();
    mockedAxios.create.mockReturnValue(mockAxiosInstance as any);
  });

  describe('getAnalytics', () => {
    it('should fetch analytics data successfully', async () => {
      const mockAnalyticsData: AnalyticsData = {
        total_items: 10,
        total_quantity: 25,
        items_by_category: [],
        items_by_location: [],
        recent_items: [],
        monthly_data: {},
        items_without_images: 0,
        items_with_images: 0,
        image_coverage: 0,
        avg_quantity: 0
      };

      const mockResponse = {
        data: { success: true, data: mockAnalyticsData }
      };

      mockAxiosInstance.get.mockResolvedValue(mockResponse);

      const result = await analyticsAPI.getAnalytics();

      expect(mockAxiosInstance.get).toHaveBeenCalledWith('/analytics');
      expect(result).toEqual(mockAnalyticsData);
    });

    it('should handle API response without data wrapper', async () => {
      const mockAnalyticsData: AnalyticsData = {
        total_items: 10,
        total_quantity: 25,
        items_by_category: [],
        items_by_location: [],
        recent_items: [],
        monthly_data: {},
        items_without_images: 0,
        items_with_images: 0,
        image_coverage: 0,
        avg_quantity: 0
      };

      mockAxiosInstance.get.mockResolvedValue({ data: mockAnalyticsData });

      const result = await analyticsAPI.getAnalytics();

      expect(result).toEqual(mockAnalyticsData);
    });

    it('should throw error when API call fails', async () => {
      const errorMessage = 'Network error';
      mockAxiosInstance.get.mockRejectedValue(new Error(errorMessage));

      await expect(analyticsAPI.getAnalytics()).rejects.toThrow(errorMessage);
    });

    it('should throw error with API error message', async () => {
      const apiError = {
        response: {
          data: {
            message: 'Server error'
          }
        }
      };

      mockAxiosInstance.get.mockRejectedValue(apiError);

      await expect(analyticsAPI.getAnalytics()).rejects.toThrow('Server error');
    });
  });

  describe('getItems', () => {
    it('should fetch items successfully', async () => {
      const mockItems: Item[] = [
        {
          id: 1,
          title: 'Test Item',
          description: 'Test Description',
          qty: 1,
          created_at: '2023-01-01',
          updated_at: '2023-01-01'
        }
      ];

      const mockResponse = {
        data: { success: true, data: mockItems }
      };

      mockAxiosInstance.get.mockResolvedValue(mockResponse);

      const result = await analyticsAPI.getItems();

      expect(mockAxiosInstance.get).toHaveBeenCalledWith('/items');
      expect(result).toEqual(mockItems);
    });
  });

  describe('getCategories', () => {
    it('should fetch categories successfully', async () => {
      const mockCategories: Category[] = [
        {
          id: 1,
          name: 'Test Category',
          color: '#ff0000',
          icon: 'fas fa-test',
          user_id: 1,
          created_at: '2023-01-01',
          updated_at: '2023-01-01'
        }
      ];

      const mockResponse = {
        data: { success: true, data: mockCategories }
      };

      mockAxiosInstance.get.mockResolvedValue(mockResponse);

      const result = await analyticsAPI.getCategories();

      expect(mockAxiosInstance.get).toHaveBeenCalledWith('/categories');
      expect(result).toEqual(mockCategories);
    });
  });

  describe('getLocations', () => {
    it('should fetch locations successfully', async () => {
      const mockLocations: Location[] = [
        {
          id: 1,
          name: 'Test Location',
          user_id: 1,
          created_at: '2023-01-01',
          updated_at: '2023-01-01'
        }
      ];

      const mockResponse = {
        data: { success: true, data: mockLocations }
      };

      mockAxiosInstance.get.mockResolvedValue(mockResponse);

      const result = await analyticsAPI.getLocations();

      expect(mockAxiosInstance.get).toHaveBeenCalledWith('/locations');
      expect(result).toEqual(mockLocations);
    });
  });
});
