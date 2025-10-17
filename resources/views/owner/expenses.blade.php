@extends('owner.olayouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Expenses</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('owner') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Expenses</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4">
          <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Add Expense</h3></div>
            <div class="card-body">
              <form id="expenseForm">
                <div class="form-group">
                  <label>Date</label>
                  <input type="date" class="form-control" name="date" required>
                </div>
                <div class="form-group">
                  <label>Category</label>
                  <input type="text" class="form-control" name="category" placeholder="e.g. Rent, Utilities" required>
                </div>
                <div class="form-group">
                  <label>Amount</label>
                  <input type="number" step="0.01" min="0" class="form-control" name="amount" required>
                </div>
                <div class="form-group">
                  <label>Brand</label>
                  <select class="form-control" name="brand_id" id="brandSelect"></select>
                </div>
                <div class="form-group">
                  <label>Branch</label>
                  <select class="form-control" name="branch_id" id="branchSelect"></select>
                </div>
                <div class="form-group">
                  <label>Notes</label>
                  <textarea class="form-control" name="notes" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Save</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title">Expenses List</h3>
              <div>
                <input type="month" id="filterMonth" class="form-control form-control-sm d-inline-block" style="width: 180px;">
                <button id="applyExpenseFilter" class="btn btn-sm btn-primary ml-2">Filter</button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped mb-0" id="expensesTable">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Category</th>
                      <th class="text-right">Amount</th>
                      <th>Brand</th>
                      <th>Branch</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            <div class="card-footer text-right">
              <strong>Total: <span id="expensesTotal">₱0.00</span></strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.querySelector('#expensesTable tbody');
  const totalEl = document.getElementById('expensesTotal');
  const form = document.getElementById('expenseForm');
  const filterMonth = document.getElementById('filterMonth');
  const brandSelect = document.getElementById('brandSelect');
  const branchSelect = document.getElementById('branchSelect');

  let brandsById = {}; let branchesById = {};

  async function loadBrandsBranches() {
    try {
      const [brandsRes, branchesRes] = await Promise.all([
        fetch('/api/brands'),
        fetch('/api/branches')
      ]);
      const brands = await brandsRes.json();
      const branches = await branchesRes.json();
      brandSelect.innerHTML = '<option value="">All Brands</option>';
      brands.forEach(b => { brandsById[b.id] = b; brandSelect.insertAdjacentHTML('beforeend', `<option value="${b.id}">${b.name}</option>`)});
      branchSelect.innerHTML = '<option value="">All Branches</option>';
      branches.forEach(br => { branchesById[br.id] = br; branchSelect.insertAdjacentHTML('beforeend', `<option value="${br.id}">${br.name}</option>`)});
    } catch (e) {
      console.error('Failed to load brands/branches', e);
    }
  }

  function monthRange(ym) {
    if (!ym) return {};
    const [y, m] = ym.split('-').map(Number);
    const from = `${y}-${String(m).padStart(2,'0')}-01`;
    const to = new Date(y, m, 0).toISOString().slice(0,10);
    return { from, to };
  }

  async function loadExpenses() {
    try {
      const {from, to} = monthRange(filterMonth.value);
      const brand_id = brandSelect.value || '';
      const branch_id = branchSelect.value || '';
      const qs = new URLSearchParams({ from: from||'', to: to||'', brand_id, branch_id }).toString();
      const res = await fetch(`/api/expenses?${qs}`);
      const json = await res.json();
      tbody.innerHTML = '';
      const list = json.data || [];
      list.forEach(exp => {
        const tr = document.createElement('tr');
        const brandName = exp.brand_id ? (brandsById[exp.brand_id]?.name || exp.brand_id) : '';
        const branchName = exp.branch_id ? (branchesById[exp.branch_id]?.name || exp.branch_id) : '';
        tr.innerHTML = `
          <td>${exp.date}</td>
          <td>${exp.category}</td>
          <td class="text-right">₱${Number(exp.amount).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})}</td>
          <td>${brandName}</td>
          <td>${branchName}</td>
          <td class="text-right">
            <button class="btn btn-xs btn-danger" data-id="${exp.id}">Delete</button>
          </td>`;
        tbody.appendChild(tr);
      });
      totalEl.textContent = `₱${Number(json.total||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})}`;

      tbody.querySelectorAll('button[data-id]').forEach(btn => {
        btn.addEventListener('click', async () => {
          if (!confirm('Delete this expense?')) return;
          const id = btn.getAttribute('data-id');
          const res = await fetch(`/api/expenses/${id}`, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          await res.json();
          await loadExpenses();
        });
      });
    } catch (e) {
      console.error('Failed to load expenses', e);
    }
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(form);
    const body = Object.fromEntries(fd.entries());
    try {
      const res = await fetch('/api/expenses', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
      const json = await res.json();
      if (!json.success) { alert('Failed to save'); return; }
      form.reset();
      await loadExpenses();
    } catch (e) {
      console.error('Failed to save expense', e);
    }
  });

  document.getElementById('applyExpenseFilter').addEventListener('click', loadExpenses);
  loadBrandsBranches().then(loadExpenses);
});
</script>
@endsection
