export interface User{
    id?: string;
    name: string;
    surname: string;
    DIN?: number;
    adders?: string;
    city?: string;
    emailP: string;
    password: string;
    Check?: boolean;
    subscribe: string;
  }

  export interface CreateUserDTO extends Omit<User, 'id'>{}

  export interface LoginModel{
    emailP: string;
    password: string;
  }
