class TableManager {
  constructor(config) {
    this.routes = config.routes;
    this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    this.uiHelper = new UIHelper(config);
    this.initialize();
  }

  initialize() {
    this.setupRowListeners();
    document.querySelector(config.formSelector).addEventListener('submit', (e) => this.handleFormSubmit(e));
  }

  async handleDelete() {
    const checked = this.uiHelper.tableBody.querySelectorAll('.rowCheckbox:checked');
    if (checked.length > 0 && confirm(`Delete ${checked.length} item(s)?`)) {
      const deletePromises = Array.from(checked).map(checkbox => {
        const row = checkbox.closest('tr');
        return fetch(this.routes.delete.replace('__ID__', row.dataset.id), {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': this.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ _method: 'DELETE' })
        });
      });

      Promise.all(deletePromises)
        .then(() => {
          checked.forEach(checkbox => checkbox.closest('tr').remove());
          this.uiHelper.updateButtonStates(0);
        })
        .catch(error => this.uiHelper.handleError(error));
    }
  }

  handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const method = form.dataset.method || 'POST';

    const formData = new FormData(form);
    formData.append('_method', method);

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': this.csrfToken,
        'Accept': 'application/json'
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.id) {
        this.uiHelper.updateTableRow(data) || this.uiHelper.addTableRow(data);
      }
      this.uiHelper.hideModal();
    })
    .catch(error => this.uiHelper.handleError(error));
  }
}