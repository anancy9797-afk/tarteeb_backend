<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Tester</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .response-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            max-height: 500px;
            overflow-y: auto;
        }

        .json-formatter {
            font-family: monospace;
            white-space: pre;
        }

        .response-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .history-item {
            cursor: pointer;
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .history-item:hover {
            background-color: #f8f9fa;
        }

        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4">API Tester</h1>

        <div class="row">
            <!-- Request Panel -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Request</h5>
                    </div>
                    <div class="card-body">
                        <form id="apiForm">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-select" id="method">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                        <option value="PUT">PUT</option>
                                        <option value="DELETE">DELETE</option>
                                        <option value="PATCH">PATCH</option>
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <input type="url" class="form-control" id="url"
                                        placeholder="Enter API URL" required>
                                </div>
                            </div>

                            <!-- Tabs for different request options -->
                            <ul class="nav nav-tabs mb-3" id="requestTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="headers-tab" data-bs-toggle="tab"
                                        data-bs-target="#headers" type="button" role="tab">Headers</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="params-tab" data-bs-toggle="tab"
                                        data-bs-target="#params" type="button" role="tab">Params</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="body-tab" data-bs-toggle="tab" data-bs-target="#body"
                                        type="button" role="tab">Body</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="auth-tab" data-bs-toggle="tab" data-bs-target="#auth"
                                        type="button" role="tab">Auth</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="requestTabContent">
                                <!-- Headers Tab -->
                                <div class="tab-pane fade show active" id="headers" role="tabpanel">
                                    <div id="headersContainer">
                                        <div class="row mb-2 header-row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Header name" name="headerName[]">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Value" name="headerValue[]">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addHeader">Add
                                        Header</button>
                                </div>

                                <!-- Params Tab -->
                                <div class="tab-pane fade" id="params" role="tabpanel">
                                    <div id="paramsContainer">
                                        <div class="row mb-2 param-row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Parameter name" name="paramName[]">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Value" name="paramValue[]">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        id="addParam">Add Parameter</button>
                                </div>

                                <!-- Body Tab -->
                                <div class="tab-pane fade" id="body" role="tabpanel">
                                    <div class="mb-3">
                                        <div class="btn-group mb-3" role="group">
                                            <input type="radio" class="btn-check" name="bodyType" id="bodyNone"
                                                autocomplete="off" checked>
                                            <label class="btn btn-outline-secondary" for="bodyNone">None</label>

                                            <input type="radio" class="btn-check" name="bodyType" id="bodyRaw"
                                                autocomplete="off">
                                            <label class="btn btn-outline-secondary" for="bodyRaw">Raw</label>

                                            <input type="radio" class="btn-check" name="bodyType"
                                                id="bodyFormData" autocomplete="off">
                                            <label class="btn btn-outline-secondary" for="bodyFormData">Form
                                                Data</label>

                                            <input type="radio" class="btn-check" name="bodyType"
                                                id="bodyFormUrlencoded" autocomplete="off">
                                            <label class="btn btn-outline-secondary"
                                                for="bodyFormUrlencoded">x-www-form-urlencoded</label>
                                        </div>

                                        <div id="bodyRawContainer" style="display: none;">
                                            <select class="form-select mb-2" id="contentType">
                                                <option value="application/json">JSON</option>
                                                <option value="text/plain">Text</option>
                                                <option value="application/xml">XML</option>
                                                <option value="text/html">HTML</option>
                                            </select>
                                            <textarea class="form-control" id="rawBody" rows="8" placeholder="Enter raw body content"></textarea>
                                        </div>

                                        <div id="bodyFormContainer" style="display: none;">
                                            <div id="formDataContainer">
                                                <!-- Form data fields will be added here -->
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="addFormField">Add Field</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Auth Tab -->
                                <div class="tab-pane fade" id="auth" role="tabpanel">
                                    <div class="mb-3">
                                        <select class="form-select" id="authType">
                                            <option value="none">No Auth</option>
                                            <option value="bearer">Bearer Token</option>
                                            <option value="basic">Basic Auth</option>
                                            <option value="apikey">API Key</option>
                                        </select>
                                    </div>

                                    <div id="bearerTokenContainer" style="display: none;">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="bearerToken"
                                                placeholder="Enter token">
                                        </div>
                                    </div>

                                    <div id="basicAuthContainer" style="display: none;">
                                        <div class="mb-3">
                                            <input type="text" class="form-control mb-2" id="username"
                                                placeholder="Username">
                                            <input type="password" class="form-control" id="password"
                                                placeholder="Password">
                                        </div>
                                    </div>

                                    <div id="apiKeyContainer" style="display: none;">
                                        <div class="mb-3">
                                            <input type="text" class="form-control mb-2" id="apiKeyName"
                                                placeholder="Key name">
                                            <input type="text" class="form-control mb-2" id="apiKeyValue"
                                                placeholder="Key value">
                                            <select class="form-select" id="apiKeyLocation">
                                                <option value="header">Header</option>
                                                <option value="query">Query Parameter</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary" id="sendBtn">Send Request</button>
                                <button type="button" class="btn btn-outline-secondary" id="saveBtn">Save</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Response & History Panel -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Response</h5>
                        <div id="statusBadge"></div>
                    </div>
                    <div class="card-body">
                        <div class="response-container">
                            <div id="responseData" class="json-formatter">Select a request and click Send to see the
                                response...</div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between">
                            <div id="responseTime" class="response-time"></div>
                            <button class="btn btn-sm btn-outline-secondary" id="copyResponse">Copy Response</button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Request History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="historyList" class="history-list" style="max-height: 250px; overflow-y: auto;">
                            <!-- History items will be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let requestHistory = JSON.parse(localStorage.getItem('apiRequestHistory') || '[]');

            // DOM elements
            const apiForm = document.getElementById('apiForm');
            const methodSelect = document.getElementById('method');
            const urlInput = document.getElementById('url');
            const headersContainer = document.getElementById('headersContainer');
            const paramsContainer = document.getElementById('paramsContainer');
            const addHeaderBtn = document.getElementById('addHeader');
            const addParamBtn = document.getElementById('addParam');
            const bodyTypeRadios = document.querySelectorAll('input[name="bodyType"]');
            const rawBodyContainer = document.getElementById('bodyRawContainer');
            const bodyFormContainer = document.getElementById('bodyFormContainer');
            const formDataContainer = document.getElementById('formDataContainer');
            const addFormFieldBtn = document.getElementById('addFormField');
            const authTypeSelect = document.getElementById('authType');
            const bearerTokenContainer = document.getElementById('bearerTokenContainer');
            const basicAuthContainer = document.getElementById('basicAuthContainer');
            const apiKeyContainer = document.getElementById('apiKeyContainer');
            const responseData = document.getElementById('responseData');
            const statusBadge = document.getElementById('statusBadge');
            const responseTime = document.getElementById('responseTime');
            const historyList = document.getElementById('historyList');
            const saveBtn = document.getElementById('saveBtn');
            const copyResponseBtn = document.getElementById('copyResponse');

            // Event listeners
            apiForm.addEventListener('submit', handleFormSubmit);
            addHeaderBtn.addEventListener('click', addHeaderRow);
            addParamBtn.addEventListener('click', addParamRow);
            addFormFieldBtn.addEventListener('click', addFormFieldRow);
            saveBtn.addEventListener('click', saveRequest);
            copyResponseBtn.addEventListener('click', copyResponseToClipboard);

            // Event listener for removing rows
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('.row').remove();
                }
            });

            // Body type change handler
            bodyTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    rawBodyContainer.style.display = 'none';
                    bodyFormContainer.style.display = 'none';

                    if (document.getElementById('bodyRaw').checked) {
                        rawBodyContainer.style.display = 'block';
                    } else if (document.getElementById('bodyFormData').checked || document
                        .getElementById('bodyFormUrlencoded').checked) {
                        bodyFormContainer.style.display = 'block';
                    }
                });
            });

            // Auth type change handler
            authTypeSelect.addEventListener('change', function() {
                bearerTokenContainer.style.display = 'none';
                basicAuthContainer.style.display = 'none';
                apiKeyContainer.style.display = 'none';

                switch (this.value) {
                    case 'bearer':
                        bearerTokenContainer.style.display = 'block';
                        break;
                    case 'basic':
                        basicAuthContainer.style.display = 'block';
                        break;
                    case 'apikey':
                        apiKeyContainer.style.display = 'block';
                        break;
                }
            });

            // Load history
            renderHistory();

            // Add initial rows
            addHeaderRow();
            addParamRow();
            addFormFieldRow();

            // Functions
            function handleFormSubmit(e) {
                e.preventDefault();

                // Reset response area
                responseData.textContent = 'Loading...';
                statusBadge.innerHTML = '';
                responseTime.textContent = '';

                // Prepare request options
                const options = {
                    method: methodSelect.value,
                    headers: getHeaders()
                };

                // Add auth headers
                addAuthHeaders(options.headers);

                // Handle body based on selected type
                if (['POST', 'PUT', 'PATCH'].includes(methodSelect.value)) {
                    if (document.getElementById('bodyRaw').checked) {
                        options.body = document.getElementById('rawBody').value;
                        options.headers['Content-Type'] = document.getElementById('contentType').value;
                    } else if (document.getElementById('bodyFormData').checked) {
                        const formData = new FormData();
                        document.querySelectorAll('#formDataContainer .form-field-row').forEach(row => {
                            const key = row.querySelector('[name="formFieldName[]"]').value;
                            const value = row.querySelector('[name="formFieldValue[]"]').value;
                            if (key) formData.append(key, value);
                        });
                        options.body = formData;
                        // Don't set Content-Type for FormData, browser will set it with boundary
                        delete options.headers['Content-Type'];
                    } else if (document.getElementById('bodyFormUrlencoded').checked) {
                        const params = new URLSearchParams();
                        document.querySelectorAll('#formDataContainer .form-field-row').forEach(row => {
                            const key = row.querySelector('[name="formFieldName[]"]').value;
                            const value = row.querySelector('[name="formFieldValue[]"]').value;
                            if (key) params.append(key, value);
                        });
                        options.body = params;
                        options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
                    }
                }

                // Build URL with query parameters
                let url = urlInput.value;
                const params = getQueryParams();
                if (Object.keys(params).length > 0) {
                    const urlObj = new URL(url);
                    Object.entries(params).forEach(([key, value]) => {
                        urlObj.searchParams.append(key, value);
                    });
                    url = urlObj.toString();
                }

                // Send request
                const startTime = performance.now();

                fetch(url, options)
                    .then(response => {
                        const endTime = performance.now();
                        const timeInMs = Math.round(endTime - startTime);

                        // Set status badge
                        const color = response.ok ? 'success' : 'danger';
                        statusBadge.innerHTML =
                            `<span class="badge bg-${color}">${response.status} ${response.statusText}</span>`;

                        // Set response time
                        responseTime.textContent = `Time: ${timeInMs}ms`;

                        // Get response headers
                        const headers = {};
                        response.headers.forEach((value, key) => {
                            headers[key] = value;
                        });

                        // Check if response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                return {
                                    body: data,
                                    headers,
                                    status: response.status,
                                    statusText: response.statusText,
                                    time: timeInMs
                                };
                            });
                        } else {
                            return response.text().then(text => {
                                return {
                                    body: text,
                                    headers,
                                    status: response.status,
                                    statusText: response.statusText,
                                    time: timeInMs
                                };
                            });
                        }
                    })
                    .then(data => {
                        // Display response
                        if (typeof data.body === 'object') {
                            responseData.textContent = JSON.stringify(data.body, null, 2);
                        } else {
                            responseData.textContent = data.body;
                        }

                        // Add to history
                        addToHistory({
                            method: methodSelect.value,
                            url: urlInput.value,
                            status: data.status,
                            time: data.time,
                            timestamp: new Date().toISOString()
                        });
                    })
                    .catch(error => {
                        responseData.textContent = `Error: ${error.message}`;
                        statusBadge.innerHTML = '<span class="badge bg-danger">Error</span>';
                    });
            }

            function getHeaders() {
                const headers = {};
                document.querySelectorAll('#headersContainer .header-row').forEach(row => {
                    const key = row.querySelector('[name="headerName[]"]').value;
                    const value = row.querySelector('[name="headerValue[]"]').value;
                    if (key) headers[key] = value;
                });
                return headers;
            }

            function getQueryParams() {
                const params = {};
                document.querySelectorAll('#paramsContainer .param-row').forEach(row => {
                    const key = row.querySelector('[name="paramName[]"]').value;
                    const value = row.querySelector('[name="paramValue[]"]').value;
                    if (key) params[key] = value;
                });
                return params;
            }

            function addAuthHeaders(headers) {
                const authType = authTypeSelect.value;

                switch (authType) {
                    case 'bearer':
                        const token = document.getElementById('bearerToken').value;
                        if (token) headers['Authorization'] = `Bearer ${token}`;
                        break;
                    case 'basic':
                        const username = document.getElementById('username').value;
                        const password = document.getElementById('password').value;
                        if (username || password) {
                            const base64Credentials = btoa(`${username}:${password}`);
                            headers['Authorization'] = `Basic ${base64Credentials}`;
                        }
                        break;
                    case 'apikey':
                        const apiKeyName = document.getElementById('apiKeyName').value;
                        const apiKeyValue = document.getElementById('apiKeyValue').value;
                        const apiKeyLocation = document.getElementById('apiKeyLocation').value;

                        if (apiKeyName && apiKeyValue && apiKeyLocation === 'header') {
                            headers[apiKeyName] = apiKeyValue;
                        }
                        break;
                }
            }

            function addHeaderRow() {
                const row = document.createElement('div');
                row.className = 'row mb-2 header-row';
                row.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Header name" name="headerName[]">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Value" name="headerValue[]">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                    </div>
                `;
                headersContainer.appendChild(row);
            }

            function addParamRow() {
                const row = document.createElement('div');
                row.className = 'row mb-2 param-row';
                row.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Parameter name" name="paramName[]">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Value" name="paramValue[]">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                    </div>
                `;
                paramsContainer.appendChild(row);
            }

            function addFormFieldRow() {
                const row = document.createElement('div');
                row.className = 'row mb-2 form-field-row';
                row.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Field name" name="formFieldName[]">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Value" name="formFieldValue[]">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                    </div>
                `;
                formDataContainer.appendChild(row);
            }

            function addToHistory(request) {
                requestHistory.unshift(request);
                // Limit history to 20 items
                requestHistory = requestHistory.slice(0, 20);
                localStorage.setItem('apiRequestHistory', JSON.stringify(requestHistory));
                renderHistory();
            }

            function renderHistory() {
                historyList.innerHTML = '';

                if (requestHistory.length === 0) {
                    historyList.innerHTML = '<div class="p-3 text-muted">No request history</div>';
                    return;
                }

                requestHistory.forEach((request, index) => {
                    const item = document.createElement('div');
                    item.className = 'history-item';
                    item.dataset.index = index;

                    const methodBadge = getMethodBadge(request.method);
                    const statusColor = request.status < 300 ? 'success' :
                        request.status < 400 ? 'warning' : 'danger';

                    item.innerHTML = `
                        ${methodBadge}
                        <span class="text-truncate d-inline-block" style="max-width: 60%;">${request.url}</span>
                        <span class="badge bg-${statusColor} float-end">${request.status || 'Error'}</span>
                    `;

                    item.addEventListener('click', () => loadRequestFromHistory(index));
                    historyList.appendChild(item);
                });
            }

            function getMethodBadge(method) {
                const colors = {
                    GET: 'success',
                    POST: 'primary',
                    PUT: 'info',
                    DELETE: 'danger',
                    PATCH: 'warning'
                };

                const color = colors[method] || 'secondary';
                return `<span class="badge bg-${color} me-2">${method}</span>`;
            }

            function loadRequestFromHistory(index) {
                const request = requestHistory[index];
                if (!request) return;

                urlInput.value = request.url;
                methodSelect.value = request.method;
            }

            function saveRequest() {
                const name = prompt('Enter a name for this request:');
                if (!name) return;

                const savedRequests = JSON.parse(localStorage.getItem('savedApiRequests') || '[]');

                savedRequests.push({
                    name,
                    method: methodSelect.value,
                    url: urlInput.value,
                    headers: getHeaders(),
                    params: getQueryParams(),
                    // Add other request fields as needed
                });

                localStorage.setItem('savedApiRequests', JSON.stringify(savedRequests));
                alert(`Request "${name}" saved successfully`);
            }

            function copyResponseToClipboard() {
                navigator.clipboard.writeText(responseData.textContent)
                    .then(() => {
                        const originalText = copyResponseBtn.textContent;
                        copyResponseBtn.textContent = 'Copied!';
                        setTimeout(() => {
                            copyResponseBtn.textContent = originalText;
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Failed to copy: ', err);
                    });
            }
        });
    </script>
</body>

</html>
