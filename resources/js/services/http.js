class HttpService {

    static async get(url, params = {}) {
        try {
            const response = await axios.get(url, { params });
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    static async post(url, data = {}, options = {}) {
        try {
            const response = await axios.post(url, data);
            if (options.toast !== false) {
                this.toast(response.data.message || 'Thành công', 'success');
            }
            if (options.reload === true) {
                setTimeout(() => window.location.reload(), options.delay || 800);
            }
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    static async put(url, data = {}, options = {}) {
        return this.submit(url, 'PUT', data, options);
    }

    static async patch(url, data = {}, options = {}) {
        return this.submit(url, 'PATCH', data, options);
    }

    static async delete(url, options = {}) {
        return this.submit(url, 'DELETE', {}, options);
    }

    static async submit(url, method, data = {}, options = {}) {
        let finalData = data;

        if (data instanceof FormData) {
            data.append('_method', method);
            finalData = data;
        } else {
            finalData = { ...data, _method: method };
        }

        return this.post(url, finalData, options);
    }

    static toast(message, type = 'success') {
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            alert(message);
        }
    }

    static handleError(error) {
        let message = 'Có lỗi xảy ra từ máy chủ';

        if (error.response) {
            const data = error.response.data;
            if (error.response.status === 422) {
                message = data.message || 'Dữ liệu không hợp lệ';
                // You can handle specific field errors here if needed
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (firstError) message = Array.isArray(firstError) ? firstError[0] : firstError;
                }
            } else if (error.response.status === 419) {
                message = 'Phiên làm việc hết hạn, vui lòng tải lại trang';
            } else if (data.message) {
                message = data.message;
            }
        }

        this.toast(message, 'error');
        console.error('HttpService Error:', error);
    }
}

window.HttpService = HttpService;

export default HttpService;
