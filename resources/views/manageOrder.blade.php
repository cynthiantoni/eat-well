<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Orders with Meal Boxes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #0b3d2e;
      color: #fff;
      margin-top: 80px;
    }

    h1 {
      font-weight: bold;
    }

    .tab-btn {
      color: #fff;
      border-color: #fff;
    }

    .tab-btn.active {
      background-color: #fff;
      color: #14532d;
      font-weight: bold;
    }

    .card {
      background-color: #fff;
      color: #000;
      border-radius: 0.75rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .card-body {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .order-header {
      display: flex;
      justify-content: flex-end;
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.5rem;
      color: #14532d;
    }

    .card-title {
      font-weight: bold;
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }

    .meal-box {
      border-radius: 0.5rem;
      padding: 0.8rem 1rem;
      margin-bottom: 1rem;
      background-color: #ebf5ee;
      color: #14532d;
    }

    .meal-box-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
    }

    .meal-entries {
      padding-left: 10px;
    }

    .meal-entry {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.3rem;
    }

    .meal-select {
      width: 140px;
      height: 32px;
      padding: 4px 12px;
      font-size: 0.9rem;
      font-weight: 700;
      border-radius: 0.5rem;
      border: 1.5px solid transparent;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      appearance: none;
      text-align-last: center;
      transition: background-color 0.3s, border-color 0.3s, color 0.3s;
    }

    .meal-select.preparing {
      background-color: #fff7e6;
      color: #8a6d0b;
      border-color: #f9d71c;
      box-shadow: 0 0 6px #f9d71c88;
    }

    .meal-select.delivering {
      background-color: #dbefff;
      color: #0b3d91;
      border-color: #4a90e2;
      box-shadow: 0 0 6px #4a90e288;
    }

    .meal-select.received {
      background-color: #d9f7e4;
      color: #1f6f3a;
      border-color: #44bb44;
      box-shadow: 0 0 6px #44bb4488;
    }

    .header-search-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .header-search-group label {
      white-space: nowrap;
      font-weight: 600;
    }

    .header-search-group .form-select-sm {
      max-width: 140px;
    }

    @media (min-width: 768px) {
      .header-search-group {
        gap: 12px;
      }
    }
  </style>
</head>
<body class="p-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h1 class="mb-3 mb-md-0">Manage Orders</h1>
    <div class="header-search-group">
      <label for="search">No. Order:</label>
      <input type="text" id="search" class="form-control form-control-sm" placeholder="Search by Order #" />
      <label for="package-select" class="mb-0">Package:</label>
      <select id="package-select" class="form-select form-select-sm">
        <option>All Packages</option>
        <option>Paket A</option>
        <option>Paket B</option>
      </select>
    </div>
  </div>

  <div class="mb-4">
    <button class="btn tab-btn active me-2" onclick="switchTab(this)">This Week</button>
    <button class="btn tab-btn btn-outline-light" onclick="switchTab(this)">Next Week</button>
  </div>

  <div class="row g-4" id="order-container"></div>

  <script>
    const orders = [
      {
        id: "INV001",
        name: "Ivan CS",
        phone: "083178723263",
        address: "Jl. Depaten Baru No. 79",
        notes: "Tolong jangan pedes ya kak!",
        packages: [
          { paket: "Paket A", qty: [3, 2, 0] },
          { paket: "Paket B", qty: [0, 0, 4] },
        ]
      },
      {
        id: "INV002",
        name: "Sarah M",
        phone: "081234567890",
        address: "Jl. Sudirman No. 10",
        notes: "Tanpa nasi ya",
        packages: [
          { paket: "Paket B", qty: [1, 1, 1] }
        ]
      },
      {
        id: "INV003",
        name: "Dedi T",
        phone: "085677889900",
        address: "Perum Griya Sejahtera Blok B3",
        notes: "Gak suka wortel",
        packages: [
          { paket: "Paket A", qty: [2, 3, 2] },
          { paket: "Paket B", qty: [1, 0, 0] }
        ]
      }
    ];

    const mealTypes = ['Breakfast', 'Lunch', 'Dinner'];
    const orderContainer = document.getElementById("order-container");

    function updateMealStatus(select) {
      const status = select.value;
      select.classList.remove("preparing", "delivering", "received");
      if (status === "Preparing") select.classList.add("preparing");
      else if (status === "Delivering") select.classList.add("delivering");
      else if (status === "Received") select.classList.add("received");
    }

    function switchTab(button) {
      document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
    }

    orders.forEach(order => {
      let mealSections = '';

      mealTypes.forEach((meal, mealIdx) => {
        // Filter paket yang qty > 0 untuk meal ini
        const filteredPkgs = order.packages.filter(pkg => pkg.qty[mealIdx] > 0);

        if (filteredPkgs.length > 0) {
          const paketList = filteredPkgs.map(pkg => {
            return `<div class="meal-entry">${pkg.paket} (${pkg.qty[mealIdx]}x)</div>`;
          }).join("");

          mealSections += `
            <div class="meal-box">
              <div class="meal-box-header">
                <span>${meal}</span>
                <select class="form-select form-select-sm meal-select preparing" onchange="updateMealStatus(this)">
                  <option>Preparing</option>
                  <option>Delivering</option>
                  <option>Received</option>
                </select>
              </div>
              <div class="meal-entries">${paketList}</div>
            </div>
          `;
        }
      });

      orderContainer.innerHTML += `
        <div class="col-12 col-md-6 col-lg-4 d-flex">
          <div class="card w-100">
            <div class="card-body">
              <div class="order-header">
                <span>Order #${order.id}</span>
              </div>
              <p class="mb-1">${order.name}</p>
              <p class="mb-1">${order.phone}</p>
              <p class="mb-1">${order.address}</p>
              <p class="mb-2 text-muted"><i>${order.notes}</i></p>
              ${mealSections}
            </div>
          </div>
        </div>
      `;
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
