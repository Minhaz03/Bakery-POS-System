<x-layouts.admin title="Open Support Ticket">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-outline btn-sm" style="padding:6px 10px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="topbar-title" style="margin:0;">Open New Support Ticket</h2>
        </div>
    </div>

    <div class="page-content">
        @if($errors->any())
            <div style="background:#fee2e2;color:#b91c1c;padding:14px 18px;border-radius:8px;margin-bottom:24px;font-size:14px;border:1px solid #fecaca;">
                <div style="font-weight:700;margin-bottom:6px;"><i class="bi bi-exclamation-triangle-fill"></i> Please fix the following errors:</div>
                <ul style="margin:0;padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">
            <!-- Left: Ticket Form -->
            <div class="card" style="border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                <div class="card-header" style="background:#f8fafc;padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                    <span style="font-size:15px;font-weight:700;color:#0f172a;"><i class="bi bi-pencil-square" style="color:var(--primary);margin-right:6px;"></i> Ticket Details</span>
                </div>
                <div class="card-body" style="padding:24px;">
                    <form action="{{ route('dashboard.tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Subject -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" for="subject" style="font-weight:600;font-size:13.5px;color:#334155;margin-bottom:6px;">
                                Subject / Summary <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" placeholder="Briefly describe the issue or inquiry (e.g. Issue generating daily sales PDF)" required style="font-size:14px;padding:10px 14px;">
                        </div>

                        <!-- Category & Priority -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                            <div>
                                <label class="form-label" for="category" style="font-weight:600;font-size:13.5px;color:#334155;margin-bottom:6px;">
                                    Category <span style="color:#ef4444;">*</span>
                                </label>
                                <select name="category" id="category" class="form-control" required style="font-size:14px;padding:10px 14px;">
                                    <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                                    <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical Support / System Bug</option>
                                    <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>Billing & Subscription</option>
                                    <option value="feature_request" {{ old('category') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                                    <option value="bug_report" {{ old('category') == 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                                    <option value="account" {{ old('category') == 'account' ? 'selected' : '' }}>Account / User Management</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="priority" style="font-weight:600;font-size:13.5px;color:#334155;margin-bottom:6px;">
                                    Priority Level <span style="color:#ef4444;">*</span>
                                </label>
                                <select name="priority" id="priority" class="form-control" required style="font-size:14px;padding:10px 14px;">
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low (Minor questions)</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Standard support)</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High (Operations impacted)</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent (POS or Business Down)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" for="message" style="font-weight:600;font-size:13.5px;color:#334155;margin-bottom:6px;">
                                Message / Description <span style="color:#ef4444;">*</span>
                            </label>
                            <textarea name="message" id="message" rows="7" class="form-control" placeholder="Please provide detailed steps to reproduce the issue, error messages, or details of your request..." required style="font-size:14px;padding:12px 14px;resize:vertical;line-height:1.6;">{{ old('message') }}</textarea>
                        </div>

                        <!-- File Attachments -->
                        <div class="form-group" style="margin-bottom:28px;">
                            <label class="form-label" style="font-weight:600;font-size:13.5px;color:#334155;margin-bottom:6px;">
                                Attachments <span style="font-size:12px;color:#64748b;font-weight:400;">(Screenshots, PDF, Log, Zip - Max 10MB per file)</span>
                            </label>
                            <div style="border:2px dashed #cbd5e1;border-radius:10px;padding:20px;text-align:center;background:#f8fafc;transition:border-color 0.2s;" ondragover="this.style.borderColor='#6366f1'" ondragleave="this.style.borderColor='#cbd5e1'">
                                <i class="bi bi-cloud-arrow-up" style="font-size:32px;color:#94a3b8;display:block;margin-bottom:8px;"></i>
                                <input type="file" name="attachments[]" id="fileInput" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.zip,.txt" style="display:none;" onchange="updateFileList(this)">
                                <label for="fileInput" class="btn btn-outline btn-sm" style="cursor:pointer;display:inline-flex;margin-bottom:6px;">
                                    <i class="bi bi-folder2-open"></i> Browse Files
                                </label>
                                <div id="selectedFiles" style="font-size:12.5px;color:#64748b;margin-top:6px;">No files selected</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;padding-top:16px;border-top:1px solid #e2e8f0;">
                            <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-outline">Cancel</a>
                            <button type="submit" class="btn btn-primary" style="padding:10px 22px;">
                                <i class="bi bi-send-fill"></i> Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Info / Help Box -->
            <div style="display:flex;flex-direction:column;gap:18px;">
                <div class="card" style="border-radius:12px;border:1px solid #e2e8f0;background:#fff;padding:20px;">
                    <div style="font-weight:700;font-size:15px;color:#0f172a;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-lightbulb" style="color:#f59e0b;font-size:18px;"></i> Helpful Tips
                    </div>
                    <ul style="font-size:13px;color:#475569;margin:0;padding-left:18px;line-height:1.6;display:flex;flex-direction:column;gap:8px;">
                        <li><strong>Be Specific:</strong> Include order numbers, product names, or exact error codes when possible.</li>
                        <li><strong>Attach Screenshots:</strong> Visual images help us identify the exact screen and issue faster.</li>
                        <li><strong>Urgent Priority:</strong> Use urgent priority if your POS terminal or checkout is completely halted.</li>
                    </ul>
                </div>

                <div class="card" style="border-radius:12px;border:1px solid #e0e7ff;background:#eef2ff;padding:20px;">
                    <div style="font-weight:700;font-size:14px;color:#3730a3;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                        <i class="bi bi-shield-check"></i> Direct Support
                    </div>
                    <p style="font-size:12.5px;color:#4338ca;margin:0;line-height:1.5;">
                        Your ticket goes straight to our platform engineering and support specialists. You will receive live in-app notifications as soon as staff responds.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileList(input) {
            const container = document.getElementById('selectedFiles');
            if (!input.files || input.files.length === 0) {
                container.innerText = 'No files selected';
                return;
            }
            let fileListHtml = '<div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:center;margin-top:6px;">';
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const sizeKb = (file.size / 1024).toFixed(1);
                fileListHtml += `<span style="background:#e2e8f0;color:#1e293b;padding:3px 8px;border-radius:6px;font-size:11.5px;display:inline-flex;align-items:center;gap:4px;"><i class="bi bi-paperclip"></i> ${file.name} (${sizeKb} KB)</span>`;
            }
            fileListHtml += '</div>';
            container.innerHTML = fileListHtml;
        }
    </script>
</x-layouts.admin>
