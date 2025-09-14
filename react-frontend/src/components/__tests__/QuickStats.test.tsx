import React from 'react';
import { render, screen } from '@testing-library/react';
import QuickStats from '@/components/QuickStats';
import { AnalyticsData } from '@/types';

describe('QuickStats', () => {
  const mockAnalytics: AnalyticsData = {
    total_items: 10,
    total_quantity: 25,
    items_by_category: [],
    items_by_location: [],
    recent_items: [
      { id: 1, title: 'Item 1', description: 'Test', qty: 5, img: 'test.jpg', created_at: '2023-01-01', updated_at: '2023-01-01' },
      { id: 2, title: 'Item 2', description: 'Test', qty: 3, created_at: '2023-01-02', updated_at: '2023-01-02' },
      { id: 3, title: 'Item 3', description: 'Test', qty: 2, img: 'test2.jpg', created_at: '2023-01-03', updated_at: '2023-01-03' }
    ],
    monthly_data: {},
    items_without_images: 1,
    items_with_images: 2,
    image_coverage: 66.7,
    avg_quantity: 2.5
  };

  it('renders quick stats with correct values', () => {
    render(<QuickStats analytics={mockAnalytics} />);
    
    expect(screen.getByText('1')).toBeInTheDocument(); // items without images
    expect(screen.getByText('2')).toBeInTheDocument(); // items with images
    expect(screen.getByText('67%')).toBeInTheDocument(); // image coverage (rounded)
    expect(screen.getByText('3')).toBeInTheDocument(); // avg quantity (rounded)
  });

  it('renders correct labels', () => {
    render(<QuickStats analytics={mockAnalytics} />);
    
    expect(screen.getByText('Without Images')).toBeInTheDocument();
    expect(screen.getByText('With Images')).toBeInTheDocument();
    expect(screen.getByText('Image Coverage')).toBeInTheDocument();
    expect(screen.getByText('Avg Quantity')).toBeInTheDocument();
  });

  it('calculates image coverage correctly', () => {
    const analyticsWithNoImages: AnalyticsData = {
      ...mockAnalytics,
      recent_items: [
        { id: 1, title: 'Item 1', description: 'Test', qty: 1, created_at: '2023-01-01', updated_at: '2023-01-01' },
        { id: 2, title: 'Item 2', description: 'Test', qty: 1, created_at: '2023-01-02', updated_at: '2023-01-02' }
      ]
    };

    render(<QuickStats analytics={analyticsWithNoImages} />);
    
    expect(screen.getByText('0%')).toBeInTheDocument(); // no images
  });

  it('calculates average quantity correctly', () => {
    const analyticsWithCustomQuantity: AnalyticsData = {
      ...mockAnalytics,
      total_items: 4,
      total_quantity: 20,
      recent_items: [
        { id: 1, title: 'Item 1', description: 'Test', qty: 5, created_at: '2023-01-01', updated_at: '2023-01-01' },
        { id: 2, title: 'Item 2', description: 'Test', qty: 5, created_at: '2023-01-02', updated_at: '2023-01-02' },
        { id: 3, title: 'Item 3', description: 'Test', qty: 5, created_at: '2023-01-03', updated_at: '2023-01-03' },
        { id: 4, title: 'Item 4', description: 'Test', qty: 5, created_at: '2023-01-04', updated_at: '2023-01-04' }
      ]
    };

    render(<QuickStats analytics={analyticsWithCustomQuantity} />);
    
    expect(screen.getByText('5')).toBeInTheDocument(); // avg quantity = 20/4 = 5
  });

  it('handles empty analytics gracefully', () => {
    const emptyAnalytics: AnalyticsData = {
      total_items: 0,
      total_quantity: 0,
      items_by_category: [],
      items_by_location: [],
      recent_items: [],
      monthly_data: {},
      items_without_images: 0,
      items_with_images: 0,
      image_coverage: 0,
      avg_quantity: 0
    };

    render(<QuickStats analytics={emptyAnalytics} />);
    
    expect(screen.getByText('0')).toBeInTheDocument();
    expect(screen.getByText('0%')).toBeInTheDocument();
  });

  it('returns null when analytics is null', () => {
    const { container } = render(<QuickStats analytics={null as any} />);
    expect(container.firstChild).toBeNull();
  });
});
