import React from 'react';
import { render, screen } from '@testing-library/react';
import MetricCard from '@/components/MetricCard';
import { MetricCardProps } from '@/types';

describe('MetricCard', () => {
  const defaultProps: MetricCardProps = {
    title: 'Test Metric',
    value: 42,
    icon: 'fas fa-test',
    color: 'primary'
  };

  it('renders with correct title and value', () => {
    render(<MetricCard {...defaultProps} />);
    
    expect(screen.getByText('Test Metric')).toBeInTheDocument();
    expect(screen.getByText('42')).toBeInTheDocument();
  });

  it('renders with correct icon', () => {
    render(<MetricCard {...defaultProps} />);
    
    const iconElement = screen.getByRole('generic', { hidden: true });
    expect(iconElement.querySelector('.fas.fa-test')).toBeInTheDocument();
  });

  it('applies correct color class', () => {
    render(<MetricCard {...defaultProps} color="success" />);
    
    const cardElement = screen.getByText('Test Metric').closest('.metric-card');
    expect(cardElement).toHaveClass('metric-success');
  });

  it('handles different color variants', () => {
    const colors: Array<MetricCardProps['color']> = ['primary', 'success', 'info', 'warning'];
    
    colors.forEach(color => {
      const { unmount } = render(<MetricCard {...defaultProps} color={color} />);
      const cardElement = screen.getByText('Test Metric').closest('.metric-card');
      expect(cardElement).toHaveClass(`metric-${color}`);
      unmount();
    });
  });

  it('displays large numbers correctly', () => {
    render(<MetricCard {...defaultProps} value={1234567} />);
    
    expect(screen.getByText('1234567')).toBeInTheDocument();
  });

  it('handles zero values', () => {
    render(<MetricCard {...defaultProps} value={0} />);
    
    expect(screen.getByText('0')).toBeInTheDocument();
  });
});
