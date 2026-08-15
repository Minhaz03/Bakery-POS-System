<x-layouts.admin title="Ticket #{{ $ticket->ticket_number }}">
    @php
        $statusBadge = $ticket->status_badge;
        $priorityBadge = $ticket->priority_badge;
    @endphp

    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-outline btn-sm" style="padding:6px 10px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <h2 class="topbar-title" style="margin:0;font-size:18px;">{{ $ticket->subject }}</h2>
                    <span style="font-family:monospace;font-weight:700;color:#4f46e5;background:#eef2ff;padding:3px 8px;border-radius:6px;font-size:12px;">
                        #{{ $ticket->ticket_number }}
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

    <div class="page-content">
        @if(session('success'))
            <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fee2e2;color:#b91c1c;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;border:1px solid #fecaca;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">
            <!-- Main Column: Message Thread & Reply Box -->
            <div style="display:flex;flex-direction:column;gap:20px;">
                <!-- Messages Timeline -->
                <div style="display:flex;flex-direction:column;gap:16px;">
                    @foreach($ticket->messages as $msg)
                        @php
                            $isStaff = $msg->isFromAdmin();
                        @endphp
                        <div class="card" style="border-radius:12px;border:1px solid {{ $isStaff ? '#c7d2fe' : '#e2e8f0' }};background:{{ $isStaff ? '#f8faff' : '#ffffff' }};overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
                            <!-- Message Header -->
                            <div style="padding:14px 18px;border-bottom:1px solid {{ $isStaff ? '#e0e7ff' : '#f1f5f9' }};display:flex;align-items:center;justify-content:space-between;background:{{ $isStaff ? '#eef2ff' : '#fafafa' }};">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:36px;height:36px;border-radius:50%;background:{{ $isStaff ? '#4f46e5' : '#0f172a' }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                                        {{ strtoupper(substr($msg->sender_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="display:flex;align-items:center;gap:6px;">
                                            <span style="font-weight:700;font-size:13.5px;color:#0f172a;">{{ $msg->sender_name }}</span>
                                            @if($isStaff)
                                                <span style="font-size:11px;font-weight:700;background:#4f46e5;color:#fff;padding:1px 6px;border-radius:4px;display:inline-flex;align-items:center;gap:3px;">
                                                    <i class="bi bi-patch-check-fill"></i> Staff Support
                                                </span>
                                            @else
                                                <span style="font-size:11px;font-weight:600;background:#e2e8f0;color:#475569;padding:1px 6px;border-radius:4px;">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size:11.5px;color:#64748b;margin-top:1px;">
                                            <i class="bi bi-clock"></i> {{ $msg->created_at->format('M d, Y · h:i A') }} ({{ $msg->created_at->diffForHumans() }})
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div style="padding:20px;font-size:14px;color:#1e293b;line-height:1.65;white-space:pre-wrap;word-break:break-word;">
                                {!! nl2br(e($msg->message)) !!}
                            </div>

                            <!-- Attachments (if any) -->
                            @if(!empty($msg->attachments) && count($msg->attachments) > 0)
                                <div style="padding:12px 20px 16px;border-top:1px dashed {{ $isStaff ? '#e0e7ff' : '#f1f5f9' }};background:{{ $isStaff ? '#f8faff' : '#fcfcfd' }};">
                                    <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                                        <i class="bi bi-paperclip"></i> Attached Files ({{ count($msg->attachments) }})
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                        @foreach($msg->attachments as $idx => $file)
                                            @php
                                                $isImg = isset($file['mime']) && str_starts_with($file['mime'], 'image/');
                                                $sizeKb = isset($file['size']) ? round($file['size'] / 1024, 1) : null;
                                            @endphp
                                            <a href="{{ route('dashboard.tickets.downloadAttachment', ['ticket' => $ticket->id, 'message' => $msg->id, 'index' => $idx]) }}" 
                                               style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #cbd5e1;border-radius:8px;color:#334155;font-size:12.5px;text-decoration:none;font-weight:600;transition:all 0.15s;" 
                                               target="_blank"
                                               onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'" 
                                               onmouseout="this.style.borderColor='#cbd5e1';this.style.color='#334155'">
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
                    @endforeach
                </div>

                <!-- Reply Box -->
                <div class="card" style="border-radius:12px;border:1px solid #e2e8f0;background:#fff;overflow:hidden;">
                    <div class="card-header" style="background:#f8fafc;padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-weight:700;font-size:14px;color:#0f172a;"><i class="bi bi-reply-fill" style="color:var(--primary);margin-right:6px;"></i> Send a Reply</span>
                        @if($ticket->isClosed())
                            <span style="font-size:12px;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:4px;">
                                Sending a reply will automatically reopen this ticket
                            </span>
                        @endif
                    </div>
                    <div class="card-body" style="padding:20px;">
                        <form action="{{ route('dashboard.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div style="margin-bottom:16px;">
                                <textarea name="message" rows="5" class="form-control" placeholder="Type your response here..." required style="font-size:14px;padding:12px 14px;resize:vertical;line-height:1.6;"></textarea>
                            </div>

                            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                                <div>
                                    <input type="file" name="attachments[]" id="replyFileInput" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.zip,.txt" style="display:none;" onchange="updateReplyFileList(this)">
                                    <label for="replyFileInput" class="btn btn-outline btn-sm" style="cursor:pointer;font-size:12.5px;">
                                        <i class="bi bi-paperclip"></i> Attach Files
                                    </label>
                                    <span id="replySelectedFiles" style="font-size:12px;color:#64748b;margin-left:8px;"></span>
                                </div>

                                <button type="submit" class="btn btn-primary" style="padding:8px 20px;">
                                    <i class="bi bi-send-fill"></i> Post Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Details & Actions -->
            <div style="display:flex;flex-direction:column;gap:18px;">
                <!-- Ticket Metadata Card -->
                <div class="card" style="border-radius:12px;border:1px solid #e2e8f0;background:#fff;padding:20px;">
                    <div style="font-weight:700;font-size:14px;color:#0f172a;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                        <span>Ticket Information</span>
                        <i class="bi bi-info-circle" style="color:#94a3b8;"></i>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:12px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Ticket Code:</span>
                            <span style="font-weight:700;font-family:monospace;color:#0f172a;">{{ $ticket->ticket_number }}</span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Category:</span>
                            <span style="font-weight:600;color:#0f172a;">{{ $ticket->category_label }}</span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Priority:</span>
                            <span style="display:inline-flex;align-items:center;gap:4px;font-weight:600;color:{{ $priorityBadge['color'] }};">
                                <i class="bi {{ $priorityBadge['icon'] }}"></i> {{ $priorityBadge['label'] }}
                            </span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Status:</span>
                            <span style="font-weight:700;color:{{ $statusBadge['color'] }};">{{ $ticket->status_label }}</span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Opened By:</span>
                            <span style="font-weight:600;color:#0f172a;">{{ $ticket->user?->name ?? 'User' }}</span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Created Date:</span>
                            <span style="color:#0f172a;">{{ $ticket->created_at->format('M d, Y · h:i A') }}</span>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="color:#64748b;">Last Activity:</span>
                            <span style="color:#0f172a;">{{ $ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Ticket Actions Card -->
                <div class="card" style="border-radius:12px;border:1px solid #e2e8f0;background:#fff;padding:20px;">
                    <div style="font-weight:700;font-size:14px;color:#0f172a;margin-bottom:12px;">Quick Actions</div>
                    
                    @if(!$ticket->isClosed())
                        <form action="{{ route('dashboard.tickets.close', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this ticket as resolved/closed?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline" style="width:100%;color:#ef4444;border-color:#fca5a5;justify-content:center;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                <i class="bi bi-check2-circle"></i> Mark Ticket as Closed
                            </button>
                        </form>
                    @else
                        <form action="{{ route('dashboard.tickets.reopen', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline" style="width:100%;color:#4f46e5;border-color:#c7d2fe;justify-content:center;" onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='transparent'">
                                <i class="bi bi-arrow-clockwise"></i> Reopen Ticket
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateReplyFileList(input) {
            const container = document.getElementById('replySelectedFiles');
            if (!input.files || input.files.length === 0) {
                container.innerText = '';
                return;
            }
            container.innerText = `${input.files.length} file(s) selected`;
        }
    </script>
</x-layouts.admin>
