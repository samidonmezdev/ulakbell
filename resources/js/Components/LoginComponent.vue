<template>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo">
                    <h1>ULAKBELL</h1>
                    <div class="logo-subtitle">Mesajlaşma Platformu</div>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Giriş Yap</h2>
                <form @submit.prevent="login" class="login-form">
                    <div class="form-group">
                        <label for="email">Email Adresi</label>
                        <input 
                            id="email"
                            type="email" 
                            v-model="email" 
                            required 
                            placeholder="ornek@email.com"
                            class="form-input"
                        />
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input 
                            id="password"
                            type="password" 
                            v-model="password" 
                            required 
                            placeholder="••••••••"
                            class="form-input"
                        />
                    </div>
                    
                    <button type="submit" class="login-btn" :disabled="isLoading">
                        <span v-if="!isLoading">Giriş Yap</span>
                        <span v-else>Giriş yapılıyor...</span>
                    </button>
                </form>
                
                <div v-if="error" class="error-message">
                    <i class="error-icon">⚠</i>
                    {{ error }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    emits: ['login-success'],
    data() {
        return {
            email: '',
            password: '',
            error: null,
            isLoading: false,
        };
    },
    methods: {
        async login() {
            if (this.isLoading) return;
            
            try {
                this.isLoading = true;
                this.error = null;
                
                const { data } = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                });
                
                const token = data?.data?.token;
                if (!token) {
                    throw new Error('Token alınamadı');
                }
                
                localStorage.setItem('token', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                this.$emit('login-success', token);
            } catch (err) {
                this.error = err.response?.data?.message || err.message || 'Hata oluştu';
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.login-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 100%;
    max-width: 400px;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.logo-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 30px;
    text-align: center;
    color: white;
}

.logo h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: 2px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.logo-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-top: 8px;
    font-weight: 300;
}

.form-section {
    padding: 40px 30px;
}

.form-section h2 {
    text-align: center;
    margin: 0 0 30px 0;
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 500;
    color: #555;
    font-size: 0.9rem;
}

.form-input {
    padding: 12px 16px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input::placeholder {
    color: #adb5bd;
}

.login-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.login-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.login-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.error-message {
    background: #fee;
    color: #c53030;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #fed7d7;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 20px;
    font-size: 0.9rem;
}

.error-icon {
    font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 480px) {
    .login-container {
        padding: 10px;
    }
    
    .login-card {
        border-radius: 15px;
    }
    
    .logo-section {
        padding: 30px 20px;
    }
    
    .logo h1 {
        font-size: 2rem;
    }
    
    .form-section {
        padding: 30px 20px;
    }
}
</style>
