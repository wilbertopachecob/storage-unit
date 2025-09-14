import React from 'react';
import { Card } from 'react-bootstrap';
import { QuickStatsProps } from '@/types';
import './QuickStats.css';

const QuickStats: React.FC<QuickStatsProps> = ({ analytics }) => {
  if (!analytics || !analytics.recent_items) {
    return null;
  }

  const items = analytics.recent_items;
  const itemsWithImages = items.filter(item => item.img && item.img.trim() !== '');
  const itemsWithoutImages = items.filter(item => !item.img || item.img.trim() === '');
  const imageCoverage = items.length > 0 ? Math.round((itemsWithImages.length / items.length) * 100) : 0;
  const avgQuantity = analytics.total_items > 0 ? Math.round(analytics.total_quantity / analytics.total_items) : 0;

  return (
    <Card className="analytics-card">
      <Card.Header>
        <h5 className="mb-0">
          <i className="fas fa-info-circle me-2"></i>
          Quick Stats
        </h5>
      </Card.Header>
      <Card.Body>
        <div className="quick-stats-grid">
          <div className="stat-item">
            <h4 className="stat-value text-primary">{itemsWithoutImages.length}</h4>
            <small className="stat-label">Without Images</small>
          </div>
          <div className="stat-item">
            <h4 className="stat-value text-success">{itemsWithImages.length}</h4>
            <small className="stat-label">With Images</small>
          </div>
          <div className="stat-item">
            <h4 className="stat-value text-info">{imageCoverage}%</h4>
            <small className="stat-label">Image Coverage</small>
          </div>
          <div className="stat-item">
            <h4 className="stat-value text-warning">{avgQuantity}</h4>
            <small className="stat-label">Avg Quantity</small>
          </div>
        </div>
      </Card.Body>
    </Card>
  );
};

export default QuickStats;
