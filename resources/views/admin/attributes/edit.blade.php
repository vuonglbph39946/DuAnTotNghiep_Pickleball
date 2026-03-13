@extends('admin.layouts.app')
@section('title', 'Sửa Thuộc tính')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center mb-4 fade-in-up">
        <a href="{{ route('admin.attributes.index') }}" class="btn btn-white shadow-sm border-0 me-3 hover-lift d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 14px; background: #fff;">
            <i class="fa-solid fa-arrow-left fs-5 text-secondary"></i>
        </a>
        <div>
            <h2 class="fw-extrabold mb-1 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                <i class="fa-solid fa-pen-to-square text-warning me-2 fs-3"></i> Cập nhật Thuộc tính <span class="text-muted ms-2 fw-medium">#{{ $attribute->id }}</span>
            </h2>
            <p class="text-muted fw-medium mb-0">Chỉnh sửa thông tin và mã màu hiển thị của nhóm phân loại</p>
        </div>
    </div>

    <form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-lg-4 mb-4">
                <div class="card premium-card border-0 h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4">Thông tin chung</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control premium-input" value="{{ $attribute->name }}" required>
                        </div>
                        <div class="alert alert-warning border-0 shadow-sm mt-4 p-3 d-flex align-items-start" style="border-radius: 12px; background-color: #fffbeb;">
                            <i class="fa-solid fa-shield-halved fs-5 text-warning me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-warning mb-1" style="color: #d97706 !important;">Bảo vệ Dữ liệu</h6>
                                <p class="mb-0 opacity-75" style="font-size: 0.85rem; color: #b45309;">Hệ thống sẽ không xóa các Giá trị đang được sử dụng ở Biến thể sản phẩm. Bạn chỉ có thể sửa tên hoặc mã màu.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card premium-card border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0">Danh sách Giá trị</h5>
                            <button type="button" class="btn btn-sm btn-warning fw-bold text-dark rounded-3 shadow-sm px-3 hover-lift" onclick="addRow()">
                                <i class="fa-solid fa-plus me-1"></i> Thêm dòng
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th width="45%" class="rounded-start">Tên Giá trị <span class="text-danger">*</span></th>
                                        <th width="35%">Mã màu</th>
                                        <th width="20%" class="text-end rounded-end">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="valuesTableBody">
                                    @foreach($attribute->values as $index => $val)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="values[{{ $index }}][id]" value="{{ $val->id }}">
                                            <input type="text" name="values[{{ $index }}][value]" class="form-control premium-input px-3" value="{{ $val->value }}" required>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="values[{{ $index }}][color_code]" class="real-color" value="{{ $val->color_code }}">
                                                <input type="color" class="form-control form-control-color fake-picker rounded-circle p-0 border-0 shadow-sm" 
                                                       style="width:38px; height:38px; cursor:pointer;" 
                                                       value="{{ $val->color_code ?? '#ffffff' }}">
                                                <button type="button" class="btn btn-sm btn-light text-danger clear-color fw-bold px-2 py-1 border" style="display: {{ $val->color_code ? 'inline-block' : 'none' }}; font-size:0.75rem; border-radius:8px;">Xóa màu</button>
                                            </div>
                                        </td>
                                        <td class="text-end"><button type="button" class="btn btn-light text-danger p-2 rounded-3 remove-row border shadow-sm"><i class="fa-solid fa-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="opacity-10 my-2">
        
        <div class="d-flex justify-content-end gap-3 mt-3 fade-in-up" style="animation-delay: 0.2s;">
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-light fw-bold hover-lift px-4 py-2 border" style="border-radius: 12px;">Hủy bỏ</a>
            <button type="submit" class="btn btn-warning text-dark fw-bold shadow-warning hover-lift px-5 py-2" style="border-radius: 12px;"><i class="fa-solid fa-check-double me-2"></i> Lưu thay đổi</button>
        </div>
    </form>
</div>

<script>
    let rowIdx = {{ count($attribute->values) }}; 
    
    function addRow() {
        const tbody = document.getElementById('valuesTableBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="values[${rowIdx}][value]" class="form-control premium-input px-3" placeholder="Nhập tên..." required></td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <input type="hidden" name="values[${rowIdx}][color_code]" class="real-color" value="">
                    <input type="color" class="form-control form-control-color fake-picker rounded-circle p-0 border-0 shadow-sm" style="width:38px; height:38px; cursor:pointer;" value="#ffffff">
                    <button type="button" class="btn btn-sm btn-light text-danger clear-color fw-bold px-2 py-1 border" style="display:none; font-size:0.75rem; border-radius:8px;">Xóa màu</button>
                </div>
            </td>
            <td class="text-end"><button type="button" class="btn btn-light text-danger p-2 rounded-3 remove-row border shadow-sm"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        rowIdx++;
        attachEvents(); 
    }

    function attachEvents() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function() { this.closest('tr').remove(); }
        });

        document.querySelectorAll('.fake-picker').forEach(picker => {
            picker.onchange = function() {
                let container = this.closest('td');
                container.querySelector('.real-color').value = this.value;
                container.querySelector('.clear-color').style.display = 'inline-block';
            }
        });

        document.querySelectorAll('.clear-color').forEach(btn => {
            btn.onclick = function() {
                let container = this.closest('td');
                container.querySelector('.real-color').value = '';
                container.querySelector('.fake-picker').value = '#ffffff';
                this.style.display = 'none';
            }
        });
    }

    attachEvents();
</script>

<style>
    .fw-extrabold { font-weight: 800; }
    .premium-card { border-radius: 20px; background: #fff; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); }
    .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    .hover-lift { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-3px); }
    .shadow-warning { box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25); }
    .premium-input { border-radius: 12px; border: 1px solid #e2e8f0; padding: 10px 16px; font-size: 0.95rem; background-color: #f8fafc; transition: all 0.3s ease; }
    .premium-input:focus { background-color: #fff; border-color: #f59e0b; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15); outline: none;}
    .form-control-color::-webkit-color-swatch-wrapper { padding: 0; }
    .form-control-color::-webkit-color-swatch { border: 2px solid #e2e8f0; border-radius: 50%; }
</style>
@endsection