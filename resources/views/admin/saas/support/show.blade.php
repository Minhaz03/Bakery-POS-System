<x-layouts.saas title="Ticket #{{ $ticket->ticket_number }}">
    @php
        $statusBadge = $ticket->status_badge;
        $priorityBadge = $ticket->priority_badge;
        $activeSub = $ticket->tenant?->activeSubscription();
        $plan = $activeSub?->plan;
    @endphp

    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .page-title-wrap { display: flex; align-items: center; gap: 12px; }
        .page-header h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0; }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 20px; }
        .card-header { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; background: #fff; font-weight: 700; font-size: 14px; color: #0f172a; display: flex; align-items: center; justify-content: space-between; }
        .card-body { padding: 20px; }

        .tab-btn { padding: 9px 18px; font-size: 13.5px; font-weight: 600; border: none; background: transparent; cursor: pointer; border-bottom: 2px solid transparent; color: #64748b; transition: all 0.15s; }
        .tab-btn.active { color: #4f46e5; border-bottom-color: #4f46e5; }
        .tab-btn.internal.active { color: #d97706; border-bottom-color: #d97706; }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <a href="{{ route('saas.tickets.index') }}" class="btn btn-outline btn-sm" style="padding:6px 10px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <h1>{{ $ticket->subject }}</h1>
                    <span style="font-family:monospace;font-weight:700;color:#4f46e5;background:#eef2ff;padding:3px 8px;border-radius:6px;font-size:12px;">
                        #{{ $ticket->ticket_number }}
                    </span>
                    <span style="font-size:12px;font-weight:600;background:#f1f5f9;color:#334155;padding:3px 8px;border-radius:6px;">
                        <i class="bi bi-buildings"></i> {{ $ticket->tenant?->name ?? 'Tenant' }}
                    </span>
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;">
            <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['color'] }};">
                <i class="bi {{ $statusBadge['icon'] }}"></i> {{ $ticket->status_label }}
            </span>
            <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:5px 12px;border-radius:20px;background:{{ $priorityBadge['bg'] }};color:{{ $priorityBadge['color'] }};">
                <i class="bi {{ $priorityBadge['icon'] }}"></i> {{ $priorityBadge['label'] }} Priority
            </span>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">
        <!-- Left: Messages Timeline & Tabbed Reply Console -->
        <div style="display:flex;flex-direction:column;gap:20px;">
            <!-- Message List -->
            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach($ticket->messages as $msg)
                    @php
                        $isAdmin = $msg->isFromAdmin();
                        $isInternal = $msg->is_internal_note;
                    @endphp
                    
                    @if($isInternal)
                        <!-- Internal Note Card -->
                        <div class="card" style="border:1.5px dashed #f59e0b;background:#fffbeb;margin-bottom:0;">
                            <div style="padding:12px 18px;border-bottom:1px dashed #fde68a;display:flex;align-items:center;justify-content:space-between;background:rgba(254,243,199,0.7);">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="display:inline-flex;align-items:center;gap:4px;background:#f59e0b;color:#fff;font-size:11px;font-weight:700;padding:2px 7px;border-radius:4px;">
                                        <i class="bi bi-lock-fill"></i> INTERNAL NOTE
                                    </span>
                                    <span style="font-weight:700;font-size:13px;color:#92400e;">{{ $msg->sender_name }}</span>
                                </div>
                                <span style="font-size:11.5px;color:#b45309;">
                                    <i class="bi bi-clock"></i> {{ $msg->created_at->format('M d, Y · h:i A') }} ({{ $msg->created_at->diffForHumans() }})
                                </span>
                            </div>
                            <div style="padding:18px 20px;font-size:13.5px;color:#78350f;line-height:1.65;white-space:pre-wrap;word-break:break-word;">
                                {!! nl2br(e($msg->message)) !!}
                            </div>
                            @if(!empty($msg->attachments) && count($msg->attachments) > 0)
                                <div style="padding:10px 18px 14px;border-top:1px dashed #fde68a;display:flex;flex-wrap:wrap;gap:8px;">
                                    @foreach($msg->attachments as $idx => $file)
                                        <a href="{{ route('saas.tickets.downloadAttachment', ['ticket' => $ticket->id, 'message' => $msg->id, 'index' => $idx]) }}" 
                                           style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;background:#fff;border:1px solid #fcd34d;border-radius:6px;color:#92400e;font-size:12px;text-decoration:none;font-weight:600;" target="_blank">
                                            <i class="bi bi-paperclip"></i> {{ $file['name'] ?? 'File' }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Public Message Card -->
                        <div class="card" style="border:1px solid {{ $isAdmin ? '#c7d2fe' : '#f1f5f9' }};background:{{ $isAdmin ? '#f8faff' : '#ffffff' }};margin-bottom:0;">
                            <div style="padding:14px 18px;border-bottom:1px solid {{ $isAdmin ? '#e0e7ff' : '#f1f5f9' }};display:flex;align-items:center;justify-content:space-between;background:{{ $isAdmin ? '#eef2ff' : '#f8fafc' }};">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:34px;height:34px;border-radius:50%;background:{{ $isAdmin ? '#4f46e5' : '#0f172a' }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                                        {{ strtoupper(substr($msg->sender_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="display:flex;align-items:center;gap:6px;">
                                            <span style="font-weight:700;font-size:13.5px;color:#0f172a;">{{ $msg->sender_name }}</span>
                                            @if($isAdmin)
                                                <span style="font-size:11px;font-weight:700;background:#4f46e5;color:#fff;padding:1px 6px;border-radius:4px;display:inline-flex;align-items:center;gap:3px;">
                                                    <i class="bi bi-patch-check-fill"></i> Staff Admin
                                                </span>
                                            @else
                                                <span style="font-size:11px;font-weight:600;background:#e2e8f0;color:#334155;padding:1px 6px;border-radius:4px;">
                                                    Tenant Customer
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size:11.5px;color:#64748b;margin-top:1px;">
                                            <i class="bi bi-clock"></i> {{ $msg->created_at->format('M d, Y · h:i A') }} ({{ $msg->created_at->diffForHumans() }})
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="padding:20px;font-size:14px;color:#1e293b;line-height:1.65;white-space:pre-wrap;word-break:break-word;">
                                {!! nl2br(e($msg->message)) !!}
                            </div>
                            @if(!empty($msg->attachments) && count($msg->attachments) > 0)
                                <div style="padding:12px 20px 16px;border-top:1px dashed {{ $isAdmin ? '#e0e7ff' : '#f1f5f9' }};background:{{ $isAdmin ? '#f8faff' : '#fcfcfd' }};">
                                    <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                                        <i class="bi bi-paperclip"></i> Attached Files ({{ count($msg->attachments) }})
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                        @foreach($msg->attachments as $idx => $file)
                                            @php
                                                $isImg = isset($file['mime']) && str_starts_with($file['mime'], 'image/');
                                                $sizeKb = isset($file['size']) ? round($file['size'] / 1024, 1) : null;
                                            @endphp
                                            <a href="{{ route('saas.tickets.downloadAttachment', ['ticket' => $ticket->id, 'message' => $msg->id, 'index' => $idx]) }}" 
                                               style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #cbd5e1;border-radius:8px;color:#334155;font-size:12.5px;text-decoration:none;font-weight:600;" 
                                               target="_blank">
                                                <i class="bi {{ $isImg ? 'bi-image' : 'bi-file-earmark-arrow-down' }}"></i>
                                                <span>{{ $file['name'] ?? 'File ' . ($idx + 1) }}</span>
                                                @if($sizeKb)
                                                    <span style="font-size:11px;color:#94a3b8;font-weight:400;">({{ $sizeKb }} KB)</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Tabbed Reply Box -->
            <div class="card">
                <div style="border-bottom:1px solid #f1f5f9;background:#f8fafc;padding:0 12px;display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;gap:4px;">
                        <button type="button" class="tab-btn active" id="tabPublicBtn" onclick="switchReplyTab('public')">
                            <i class="bi bi-reply-fill"></i> Reply to Tenant
                        </button>
                        <button type="button" class="tab-btn internal" id="tabInternalBtn" onclick="switchReplyTab('internal')">
                            <i class="bi bi-lock-fill"></i> Internal Staff Note
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Public Reply Form -->
                    <form id="publicReplyForm" action="{{ route('saas.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="is_internal_note" value="0">

                        <div style="margin-bottom:16px;">
                            <textarea name="message" rows="5" class="form-control" placeholder="Write official response to the customer..." required style="font-size:14px;padding:12px 14px;resize:vertical;line-height:1.6;"></textarea>
                        </div>

                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <label style="font-size:12.5px;font-weight:600;color:#64748b;">Set Status:</label>
                                    <select name="status" class="form-control" style="width:auto;padding:5px 10px;font-size:12.5px;">
                                        <option value="answered" selected>Answered (Customer Turn)</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>

                                <div>
                                    <input type="file" name="attachments[]" id="publicFileInput" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.zip,.txt" style="display:none;" onchange="updateFileName(this, 'publicFileName')">
                                    <label for="publicFileInput" class="btn btn-outline btn-sm" style="cursor:pointer;font-size:12px;">
                                        <i class="bi bi-paperclip"></i> Attach Files
                                    </label>
                                    <span id="publicFileName" style="font-size:12px;color:#64748b;margin-left:6px;"></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding:8px 20px;">
                                <i class="bi bi-send-fill"></i> Send Response
                            </button>
                        </div>
                    </form>

                    <!-- Internal Note Form (Hidden by default) -->
                    <form id="internalNoteForm" action="{{ route('saas.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" style="display:none;">
                        @csrf
                        <input type="hidden" name="is_internal_note" value="1">

                        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 14px;margin-bottom:14px;font-size:12.5px;color:#92400e;display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-shield-lock-fill" style="font-size:16px;"></i>
                            <span><strong>Internal Note:</strong> This content is strictly confidential and will only be seen by SaaS superadmins and staff.</span>
                        </div>

                        <div style="margin-bottom:16px;">
                            <textarea name="message" rows="4" class="form-control" placeholder="Add private staff observation, troubleshooting notes, or escalation details..." required style="font-size:14px;padding:12px 14px;resize:vertical;line-height:1.6;border-color:#fcd34d;background:#fffdfa;"></textarea>
                        </div>

                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                            <div>
                                <input type="file" name="attachments[]" id="internalFileInput" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.zip,.txt" style="display:none;" onchange="updateFileName(this, 'internalFileName')">
                                <label for="internalFileInput" class="btn btn-outline btn-sm" style="cursor:pointer;font-size:12px;">
                                    <i class="bi bi-paperclip"></i> Attach Internal File
                                </label>
                                <span id="internalFileName" style="font-size:12px;color:#64748b;margin-left:6px;"></span>
                            </div>

                            <button type="submit" class="btn" style="background:#d97706;color:#fff;padding:8px 20px;">
                                <i class="bi bi-lock-fill"></i> Save Internal Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Controls & Tenant Context -->
        <div style="display:flex;flex-direction:column;gap:18px;">
            <!-- Quick Management Controls -->
            <div class="card">
                <div class="card-header">
                    <span>Ticket Management</span>
                    <i class="bi bi-sliders2"></i>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
                    <!-- Status Changer -->
                    <form action="{{ route('saas.tickets.updateStatus', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="form-label" style="font-size:12.5px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">Change Status</label>
                        <div style="display:flex;gap:6px;">
                            <select name="status" class="form-control" style="font-size:13px;flex:1;">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="answered" {{ $ticket->status === 'answered' ? 'selected' : '' }}>Staff Answered</option>
                                <option value="tenant_reply" {{ $ticket->status === 'tenant_reply' ? 'selected' : '' }}>Customer Reply</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <button type="submit" class="btn btn-outline btn-sm">Update</button>
                        </div>
                    </form>

                    <!-- Priority Changer -->
                    <form action="{{ route('saas.tickets.updatePriority', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="form-label" style="font-size:12.5px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">Change Priority</label>
                        <div style="display:flex;gap:6px;">
                            <select name="priority" class="form-control" style="font-size:13px;flex:1;">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            <button type="submit" class="btn btn-outline btn-sm">Update</button>
                        </div>
                    </form>

                    <!-- Staff Assignment -->
                    <form action="{{ route('saas.tickets.assign', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="form-label" style="font-size:12.5px;font-weight:700;color:#334155;margin-bottom:4px;display:block;">Assign Support Staff</label>
                        <div style="display:flex;gap:6px;">
                            <select name="assigned_to" class="form-control" style="font-size:13px;flex:1;">
                                <option value="">-- Unassigned --</option>
                                @foreach($admins as $a)
                                    <option value="{{ $a->id }}" {{ $ticket->assigned_to == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline btn-sm">Assign</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tenant Profile Summary -->
            <div class="card">
                <div class="card-header">
                    <span>Tenant Overview</span>
                    <i class="bi bi-building"></i>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:12px;font-size:13px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Tenant Name:</span>
                        <a href="{{ route('saas.tenants.show', $ticket->tenant_id) }}" style="font-weight:700;color:#4f46e5;text-decoration:none;">
                            {{ $ticket->tenant?->name ?? 'N/A' }} <i class="bi bi-box-arrow-up-right" style="font-size:10px;"></i>
                        </a>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Subscribed Plan:</span>
                        <span style="font-weight:600;color:#0f172a;background:#f1f5f9;padding:2px 8px;border-radius:4px;">
                            {{ $plan?->name ?? 'No active plan' }}
                        </span>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Contact User:</span>
                        <span style="font-weight:600;color:#0f172a;">{{ $ticket->user?->name ?? 'N/A' }}</span>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">User Email:</span>
                        <span style="color:#0f172a;font-size:12px;">{{ $ticket->user?->email ?? 'N/A' }}</span>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Created Date:</span>
                        <span style="color:#0f172a;">{{ $ticket->created_at->format('M d, Y') }}</span>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Category:</span>
                        <span style="font-weight:600;color:#0f172a;">{{ $ticket->category_label }}</span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone: Delete Ticket -->
            <div class="card" style="border:1px solid #fee2e2;background:#fff;">
                <div class="card-body">
                    <form action="{{ route('saas.tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Permanently delete this support ticket, all discussion logs, and uploaded files? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline" style="width:100%;color:#ef4444;border-color:#fca5a5;justify-content:center;font-size:13px;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                            <i class="bi bi-trash"></i> Delete Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchReplyTab(tab) {
            const publicForm = document.getElementById('publicReplyForm');
            const internalForm = document.getElementById('internalNoteForm');
            const publicBtn = document.getElementById('tabPublicBtn');
            const internalBtn = document.getElementById('tabInternalBtn');

            if (tab === 'public') {
                publicForm.style.display = 'block';
                internalForm.style.display = 'none';
                publicBtn.classList.add('active');
                internalBtn.classList.remove('active');
            } else {
                publicForm.style.display = 'none';
                internalForm.style.display = 'block';
                publicBtn.classList.remove('active');
                internalBtn.classList.add('active');
            }
        }

        function updateFileName(input, targetId) {
            const el = document.getElementById(targetId);
            if (!input.files || input.files.length === 0) {
                el.innerText = '';
                return;
            }
            el.innerText = `${input.files.length} file(s) selected`;
        }
    </script>
</x-layouts.saas>
