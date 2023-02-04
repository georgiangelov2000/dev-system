import React from 'react';
import ReactDOM from 'react-dom';
import ModalFormOne from './ModalFormOne';

export default function Category() {
    return (
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-md-8">
                        <div className="card">
                            <div className="card-header">Example Component</div>
                            <div className="card-body">I'm an example component!</div>
                            <button type="button" className="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                Launch demo modal
                            </button>
                        </div>
                    </div>
                </div>
                <ModalFormOne />
            </div>
            );
}

if (document.getElementById('categories-page')) {
    ReactDOM.render(<Category />, document.getElementById('categories-page'));
}
