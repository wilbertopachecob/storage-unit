import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Spinner, Alert } from 'react-bootstrap';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement, PointElement, LineElement } from 'chart.js';
import { Doughnut, Bar, Line } from 'react-chartjs-2';
import { analyticsAPI } from '../services/api';
import { AnalyticsData, ChartData } from '../types';
import MetricCard from './MetricCard';
import RecentItems from './RecentItems';
import QuickStats from './QuickStats';
import './AnalyticsDashboard.css';

// Register Chart.js components
ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement
);

const AnalyticsDashboard: React.FC = () => {
  const [analytics, setAnalytics] = useState<AnalyticsData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchAnalytics();
  }, []);

  const fetchAnalytics = async (): Promise<void> => {
    try {
      setLoading(true);
      setError(null);
      const data = await analyticsAPI.getAnalytics();
      setAnalytics(data);
    } catch (err: any) {
      setError(err.message || 'Failed to load analytics data');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="analytics-container">
        <Container>
          <div className="loading-spinner">
            <Spinner animation="border" variant="light" />
            <span className="ms-3 text-white">Loading analytics...</span>
          </div>
        </Container>
      </div>
    );
  }

  if (error) {
    return (
      <div className="analytics-container">
        <Container>
          <Alert variant="danger" className="error-message">
            <Alert.Heading>Error Loading Analytics</Alert.Heading>
            <p>{error}</p>
            <button className="btn btn-primary" onClick={fetchAnalytics}>
              Try Again
            </button>
          </Alert>
        </Container>
      </div>
    );
  }

  if (!analytics) {
    return (
      <div className="analytics-container">
        <Container>
          <Alert variant="info">
            No analytics data available. Please add some items to see analytics.
          </Alert>
        </Container>
      </div>
    );
  }

  // Prepare chart data
  const categoryChartData: ChartData = {
    labels: analytics.items_by_category.map(item => item.name),
    datasets: [{
      data: analytics.items_by_category.map(item => item.count),
      backgroundColor: analytics.items_by_category.map(item => item.color),
      borderWidth: 2,
      borderColor: '#fff'
    }]
  };

  const locationChartData: ChartData = {
    labels: analytics.items_by_location.map(item => item.name),
    datasets: [{
      label: 'Items',
      data: analytics.items_by_location.map(item => item.count),
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  };

  // Calculate monthly data for line chart
  const monthlyData: Record<string, number> = analytics.recent_items.reduce((acc: Record<string, number>, item) => {
    const month = new Date(item.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    acc[month] = (acc[month] || 0) + 1;
    return acc;
  }, {});

  const monthlyChartData: ChartData = {
    labels: Object.keys(monthlyData),
    datasets: [{
      label: 'Items Added',
      data: Object.values(monthlyData),
      borderColor: 'rgba(75, 192, 192, 1)',
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      tension: 0.1,
      fill: true
    }]
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom' as const
      }
    }
  };

  const barOptions = {
    ...chartOptions,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  };

  return (
    <div className="analytics-container">
      <Container>
        <h1 className="page-title">
          <i className="fas fa-chart-bar me-3"></i>
          Analytics Dashboard
        </h1>

        {/* Key Metrics */}
        <Row className="mb-4">
          <Col md={3} className="mb-3">
            <MetricCard
              title="Total Items"
              value={analytics.total_items}
              icon="fas fa-box"
              color="primary"
            />
          </Col>
          <Col md={3} className="mb-3">
            <MetricCard
              title="Total Quantity"
              value={analytics.total_quantity}
              icon="fas fa-cubes"
              color="success"
            />
          </Col>
          <Col md={3} className="mb-3">
            <MetricCard
              title="Categories"
              value={analytics.items_by_category.length}
              icon="fas fa-tags"
              color="info"
            />
          </Col>
          <Col md={3} className="mb-3">
            <MetricCard
              title="Locations"
              value={analytics.items_by_location.length}
              icon="fas fa-map-marker-alt"
              color="warning"
            />
          </Col>
        </Row>

        <Row>
          {/* Items by Category Chart */}
          <Col md={6} className="mb-4">
            <Card className="analytics-card">
              <Card.Header>
                <h5 className="mb-0">
                  <i className="fas fa-tags me-2"></i>
                  Items by Category
                </h5>
              </Card.Header>
              <Card.Body>
                {analytics.items_by_category.length > 0 ? (
                  <div className="chart-container">
                    <Doughnut data={categoryChartData} options={chartOptions} />
                  </div>
                ) : (
                  <p className="text-muted text-center">No categories yet</p>
                )}
              </Card.Body>
            </Card>
          </Col>

          {/* Items by Location Chart */}
          <Col md={6} className="mb-4">
            <Card className="analytics-card">
              <Card.Header>
                <h5 className="mb-0">
                  <i className="fas fa-map-marker-alt me-2"></i>
                  Items by Location
                </h5>
              </Card.Header>
              <Card.Body>
                {analytics.items_by_location.length > 0 ? (
                  <div className="chart-container">
                    <Bar data={locationChartData} options={barOptions} />
                  </div>
                ) : (
                  <p className="text-muted text-center">No locations yet</p>
                )}
              </Card.Body>
            </Card>
          </Col>
        </Row>

        <Row>
          {/* Monthly Items Added */}
          <Col md={8} className="mb-4">
            <Card className="analytics-card">
              <Card.Header>
                <h5 className="mb-0">
                  <i className="fas fa-calendar-alt me-2"></i>
                  Items Added Over Time
                </h5>
              </Card.Header>
              <Card.Body>
                {Object.keys(monthlyData).length > 0 ? (
                  <div className="chart-container">
                    <Line data={monthlyChartData} options={barOptions} />
                  </div>
                ) : (
                  <p className="text-muted text-center">No data available</p>
                )}
              </Card.Body>
            </Card>
          </Col>

          {/* Quick Stats */}
          <Col md={4} className="mb-4">
            <QuickStats analytics={analytics} />
          </Col>
        </Row>

        {/* Recent Items */}
        <Row>
          <Col xs={12}>
            <RecentItems items={analytics.recent_items} />
          </Col>
        </Row>
      </Container>
    </div>
  );
};

export default AnalyticsDashboard;
