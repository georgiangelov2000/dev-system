const statusPaymentsWithIcons = {
    1: { label: "Paid", iconClass: "fal fa-check-circle" },
    2: { label: "Pending", iconClass: "fal fa-hourglass-half" },
    3: { label: "Partially Paid", iconClass: "fal fa-money-bill-alt" },
    4: { label: "Overdue", iconClass: "fal fa-exclamation-circle" },
    5: { label: "Refunded", iconClass: "fal fa-undo-alt" },
}

const paymentStatuses = {
    1: { label: "Paid"},
    2: { label: "Pending"},
    3: { label: "Partially Paid"},
    4: { label: "Overdue"},
    5: { label: "Refunded"},
}

const deliveryStatusesWithIcons = {
    1: { label: "Delivered", iconClass: "fa-light fa-truck"},
    2: { label: "Pending", iconClass: "fal fa-hourglass-half"},
    4: { label: "Overdue", iconClass: "fal fa-exclamation-circle"},
}
const deliveryStatuses = {
    1: { label: "Delivered" },
    2: { label: "Pending" },
    4: { label: "Overdue",},
}

const paymentMethods = {
    1: "Cash",
    2: "Bank Transfer",
    3: "Credit Card",
    4: "Cheque",
    5: "Online Payment"
}

export { statusPaymentsWithIcons, paymentMethods, paymentStatuses, deliveryStatusesWithIcons, deliveryStatuses };
