import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import { analyticsAPI } from '@/services/api';
import AnalyticsDashboard from '@/components/AnalyticsDashboard';
import { AnalyticsData } from '@/types';

// Mock Chart.js
jest.mock('chart.js', () => ({
  Chart: {
    register: jest.fn(),
  },
  ArcElement: jest.fn(),
  Tooltip: jest.fn(),
  Legend: jest.fn(),
  CategoryScale: jest.fn(),
  LinearScale: jest.fn(),
  BarElement: jest.fn(),
  PointElement: jest.fn(),
  LineElement: jest.fn(),
}));

// Mock react-chartjs-2
jest.mock('react-chartjs-2', () => ({
  Doughnut: ({ data, options }: any) => <div data-testid="doughnut-chart" data-chart-data={JSON.stringify(data)} />,
  Bar: ({ data, options }: any) => <div data-testid="bar-chart" data-chart-data={JSON.stringify(data)} />,
  Line: ({ data, options }: any) => <div data-testid="line-chart" data-chart-data={JSON.stringify(data)} />,
}));

// Mock the API
jest.mock('@/services/api');
const mockAnalyticsAPI = analyticsAPI as jest.Mocked<typeof analyticsAPI>;

describe('AnalyticsDashboard', () => {
  const mockAnalyticsData: AnalyticsData = {
    total_items: 15,
    total_quantity: 45,
    items_by_category: [
      { name: 'Tools', color: '#ff6384', count: 5 },
      { name: 'Electronics', color: '#36a2eb', count: 3 },
      { name: 'Furniture', color: '#ffce56', count: 7 }
    ],
    items_by_location: [
      { name: 'Garage', count: 8 },
      { name: 'Basement', count: 4 },
      { name: 'Attic', count: 3 }
    ],
    recent_items: [
      { id: 1, title: 'Hammer', description: 'A good hammer', qty: 1, img: 'hammer.jpg', created_at: '2023-01-01', updated_at: '2023-01-01' },
      { id: 2, title: 'Screwdriver', description: 'Phillips head', qty: 2, created_at: '2023-01-02', updated_at: '2023-01-02' }
    ],
    monthly_data: { 'Jan 2023': 2, 'Feb 2023': 3 },
    items_without_images: 1,
    items_with_images: 1,
    image_coverage: 50,
    avg_quantity: 3
  };

  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('renders loading state initially', () => {
    mockAnalyticsAPI.getAnalytics.mockImplementation(() => new Promise(() => {})); // Never resolves
    
    render(<AnalyticsDashboard />);
    
    expect(screen.getByText('Loading analytics...')).toBeInTheDocument();
    expect(screen.getByRole('status')).toBeInTheDocument(); // Spinner
  });

  it('renders analytics data when loaded successfully', async () => {
    mockAnalyticsAPI.getAnalytics.mockResolvedValue(mockAnalyticsData);
    
    render(<AnalyticsDashboard />);
    
    await waitFor(() => {
      expect(screen.getByText('Analytics Dashboard')).toBeInTheDocument();
    });
    
    // Check metric cards
    expect(screen.getByText('15')).toBeInTheDocument(); // Total items
    expect(screen.getByText('45')).toBeInTheDocument(); // Total quantity
    expect(screen.getByText('3')).toBeInTheDocument(); // Categories
    expect(screen.getByText('3')).toBeInTheDocument(); // Locations
  });

  it('renders error state when API fails', async () => {
    const errorMessage = 'Failed to fetch analytics';
    mockAnalyticsAPI.getAnalytics.mockRejectedValue(new Error(errorMessage));
    
    render(<AnalyticsDashboard />);
    
    await waitFor(() => {
      expect(screen.getByText('Error Loading Analytics')).toBeInTheDocument();
      expect(screen.getByText(errorMessage)).toBeInTheDocument();
    });
    
    expect(screen.getByText('Try Again')).toBeInTheDocument();
  });

  it('renders no data message when analytics is null', async () => {
    mockAnalyticsAPI.getAnalytics.mockResolvedValue(null as any);
    
    render(<AnalyticsDashboard />);
    
    await waitFor(() => {
      expect(screen.getByText('No analytics data available. Please add some items to see analytics.')).toBeInTheDocument();
    });
  });

  it('displays charts when data is available', async () => {
    mockAnalyticsAPI.getAnalytics.mockResolvedValue(mockAnalyticsData);
    
    render(<AnalyticsDashboard />);
    
    await waitFor(() => {
      expect(screen.getByTestId('doughnut-chart')).toBeInTheDocument();
      expect(screen.getByTestId('bar-chart')).toBeInTheDocument();
      expect(screen.getByTestId('line-chart')).toBeInTheDocument();
    });
  });

  it('shows no categories message when no categories exist', async () => {
    const analyticsWithoutCategories = {
      ...mockAnalyticsData,
      items_by_category: []
    };
    mockAnalyticsAPI.getAnalytics.mockResolvedValue(analyticsWithoutCategories);
    
    render(<AnalyticsDashboard />);
    
    await waitFor(() => {
      expect(screen.getByText('No categories yet')).toBeInTheDocument();
    });
  });

  it('shows no locations message when no locations exist', async () => {
    const analyticsWithoutLocations = {
      ...mockAnalyticsData,
      items_by_location: []
    };
    mockAnalyticsAPI.getAnalytics.mockResolvedValue(analyticsWithoutLocations);
    
    render(<AnalyticsDashboard />);
    
    await waitFor(() => {
      expect(screen.getByText('No locations yet')).toBeInTheDocument();
    });
  });

  it('handles retry functionality', async () => {
    mockAnalyticsAPI.getAnalytics
      .mockRejectedValueOnce(new Error('Network error'))
      .mockResolvedValueOnce(mockAnalyticsData);
    
    render(<AnalyticsDashboard />);
    
    // Wait for error state
    await waitFor(() => {
      expect(screen.getByText('Try Again')).toBeInTheDocument();
    });
    
    // Click retry button
    screen.getByText('Try Again').click();
    
    // Wait for success state
    await waitFor(() => {
      expect(screen.getByText('Analytics Dashboard')).toBeInTheDocument();
    });
    
    expect(mockAnalyticsAPI.getAnalytics).toHaveBeenCalledTimes(2);
  });
});
