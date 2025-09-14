import React from 'react';
import { Card, Row, Col } from 'react-bootstrap';
import { RecentItemsProps, Item } from '@/types';
import './RecentItems.css';

const RecentItems: React.FC<RecentItemsProps> = ({ items }) => {
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
                  {item.img && (
                    <div className="item-image">
                      <img 
                        src={`${process.env.REACT_APP_API_URL || 'http://localhost:8080'}/uploads/${item.img}`} 
                        alt={item.title}
                        className="img-thumbnail"
                      />
                    </div>
                  )}
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
