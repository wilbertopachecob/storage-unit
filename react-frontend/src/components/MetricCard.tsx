import React from 'react';
import { MetricCardProps } from '@/types';
import './MetricCard.css';

const MetricCard: React.FC<MetricCardProps> = ({ 
  title, 
  value, 
  icon, 
  color = 'primary' 
}) => {
  const colorClasses: Record<string, string> = {
    primary: 'metric-primary',
    success: 'metric-success',
    info: 'metric-info',
    warning: 'metric-warning'
  };

  return (
    <div className={`metric-card ${colorClasses[color]}`}>
      <div className="metric-icon">
        <i className={icon}></i>
      </div>
      <div className="metric-content">
        <h2 className="metric-value">{value}</h2>
        <p className="metric-title">{title}</p>
      </div>
    </div>
  );
};

export default MetricCard;
