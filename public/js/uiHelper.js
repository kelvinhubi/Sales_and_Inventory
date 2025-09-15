class UIHelper {
  constructor(config) {
    this.modal = new bootstrap.Modal(document.querySelector(config.modalSelector));
    this.errorElement = document.querySelector(config.errorSelector);
    this.tableBody = document.querySelector(config.tableBodySelector);
    this.selectAll = document.querySelector(config.selectAllSelector);
  }

  showModal() {
    this.errorElement.classList.add('d-none');
    this.modal.show();
  }

  hideModal() {
    this.modal.hide();
  }

  updateTableRow(data) {
    const existingRow = this.tableBody.querySelector(`tr[data-id='${data.id}']`);
    if (existingRow) {
      existingRow.cells[1].textContent = data.name;
      existingRow.cells[2].textContent = data.price;
      existingRow.cells[3].textContent = data.perishable_status ? 'Yes' : 'No';
    }
  }

  addTableRow(data) {
    const newRow = document.createElement('tr');
    newRow.dataset.id = data.id;
    newRow.innerHTML = `
      <td><input type="checkbox" class="rowCheckbox"></td>
      <td>${data.name}</td>
      <td>${data.price}</td>
      <td>${data.perishable_status ? 'Yes' : 'No'}</td>
    `;
    this.tableBody.appendChild(newRow);
  }

  handleError(error) {
    this.errorElement.textContent = error.message || 'An error occurred';
    this.errorElement.classList.remove('d-none');
  }

  updateButtonStates(checkedCount) {
    this.editBtn.disabled = checkedCount !== 1;
    this.deleteBtn.disabled = checkedCount === 0;
  }
}