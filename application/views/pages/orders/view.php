<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Orders</a></li>
            <li class="breadcrumb-item active">Order Details</li>
        </ol>
    </nav>
    <div>
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-auto">
                <h2 class="text-bold text-body-emphasis">Order Details</h2>
            </div>
        </div>
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
            <div class="row my-6">
                <div class="col-md-8">
                    <table class="table fs-9 table-sm table-borderless" id="order-details">
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <form id="form" onsubmit="updateOrderStatus(event)" enctype="multipart/form-data" method="post">
                        <input type="hidden" id="id" name="id">
                        <div class="form-group mb-2">
                            <label for="status" class="fs-9 fw-bold">Order Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Choose</option>
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                            <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-status"></p>
                        </div>
                        <div class="form-group mb-2">
                            <!-- Payment Status select -->
                            <label for="payment_status" class="fs-9 fw-bold">Payment Status</label>
                            <select id="payment_status" name="payment_status" class="form-control">
                                <option value="">Choose</option>
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                                <option value="Failed">Failed</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                            <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-payment_status"></p>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" id="submit-btn">Update Order</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-md-12">
                    <h5 class="card-title my-2">Items Details</h5>
                    <div class="table-responsive scrollbar ms-n1 ps-1">
                        <table class="table fs-9 mb-0 gy-1" id="order-table" style="white-space: no-wrap;">
                            <thead>
                                <tr class="bg-light">
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody id="order-table-tbody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>