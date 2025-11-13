export function validatePassword(password) {

    const errors = []

    if (!password) {
        errors.push('A senha é obrigatória!')
        return errors
    }

    if (password.length < 8) {
        errors.push('A senha deve ter pelo menos 8 caracteres!')
    }

    if (!/[A-Za-z]/.test(password) || !/\d/.test(password)) {
        errors.push('A senha deve conter letras e números!')
    }

    if (!/[A-Z]/.test(password)) 
        errors.push('A senha deve ter ao menos uma letra maiúscula!')

    return errors
}
