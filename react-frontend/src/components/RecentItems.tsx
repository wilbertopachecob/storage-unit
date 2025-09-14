import React, { useState } from 'react';
import { Card, Row, Col } from 'react-bootstrap';
import { RecentItemsProps, Item } from '@/types';
import './RecentItems.css';

const RecentItems: React.FC<RecentItemsProps> = ({ items }) => {
  const [imageErrors, setImageErrors] = useState<Set<number>>(new Set());

  if (!items || items.length === 0) {
    return (
      <Card className="analytics-card">
        <Card.Header>
          <h5 className="mb-0">
            <i className="fas fa-clock me-2"></i>
            Recent Items
          </h5>
        </Card.Header>
        <Card.Body>
          <p className="text-muted text-center">No items yet</p>
        </Card.Body>
      </Card>
    );
  }

  const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  };

  const handleImageError = (itemId: number) => {
    setImageErrors(prev => new Set(prev).add(itemId));
  };

  const renderItemImage = (item: Item) => {
    const hasImage = item.img && item.img.trim() !== '';
    const hasError = imageErrors.has(item.id);

    if (!hasImage || hasError) {
      // Show a proper placeholder with icon instead of text
      return (
        <div className="item-image-placeholder">
          <i className="fas fa-image text-muted"></i>
          <small className="d-block text-muted mt-1">No Image</small>
        </div>
      );
    }

    return (
      <div className="item-image">
        <img 
          src={`/uploads/${item.img}`} 
          alt={item.title}
          className="img-thumbnail"
          onError={() => handleImageError(item.id)}
        />
      </div>
    );
  };

  return (
    <Card className="analytics-card">
      <Card.Header>
        <h5 className="mb-0">
          <i className="fas fa-clock me-2"></i>
          Recent Items
        </h5>
      </Card.Header>
      <Card.Body>
        <Row>
          {items.map((item: Item, index: number) => (
            <Col md={6} lg={4} key={index} className="mb-3">
              <div className="recent-item-card">
                <div className="item-header">
                  <h6 className="item-title">{item.title}</h6>
                  {renderItemImage(item)}
                </div>
                <div className="item-details">
                  <p className="item-info">
                    <small className="text-muted">
                      {item.category_name ? (
                        <span 
                          className="category-badge" 
                          style={{ backgroundColor: item.category_color }}
                        >
                          {item.category_name}
                        </span>
                      ) : (
                        <span className="text-muted">No Category</span>
                      )}
                    </small>
                  </p>
                  <p className="item-info">
                    <small className="text-muted">
                      {item.location_name ? (
                        <span>📍 {item.location_name}</span>
                      ) : (
                        <span className="text-muted">No Location</span>
                      )}
                    </small>
                  </p>
                  <p className="item-info">
                    <small className="text-muted">
                      <strong>Quantity:</strong> {item.qty}
                    </small>
                  </p>
                  <p className="item-info">
                    <small className="text-muted">
                      <strong>Added:</strong> {formatDate(item.created_at)}
                    </small>
                  </p>
                </div>
              </div>
            </Col>
          ))}
        </Row>
      </Card.Body>
    </Card>
  );
};

export default RecentItems;
