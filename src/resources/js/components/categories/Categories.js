import React from 'react';
import ReactDOM from 'react-dom';
import { createRoot } from 'react-dom/client';

function Categories() {
  return (
          <div className="col-md-12">

          </div>
    );
}

const container = document.getElementById('categoriesSection');
const root = createRoot(container);
root.render(<Categories />);