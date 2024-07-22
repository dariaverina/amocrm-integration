import React, { useEffect } from 'react';
import './Notification.css';

const Notification = ({ message, onClose, type }) => {
  useEffect(() => {
    const timer = setTimeout(onClose, 3000);
    return () => clearTimeout(timer);
  }, [onClose]);

  return (
    <div className={`notification ${type === 'error' ? 'notification-error' : 'notification-success'}`}>
      {message}
    </div>
  );
};

export default Notification;
