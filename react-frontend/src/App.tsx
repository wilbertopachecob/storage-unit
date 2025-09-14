import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import AnalyticsDashboard from './components/AnalyticsDashboard';
import './App.css';

const App: React.FC = () => {
  return (
    <Router>
      <div className="App">
        <Routes>
          <Route path="/" element={<AnalyticsDashboard />} />
          <Route path="/analytics" element={<AnalyticsDashboard />} />
        </Routes>
      </div>
    </Router>
  );
};

export default App;
