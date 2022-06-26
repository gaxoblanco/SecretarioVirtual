export interface User{
    id: string;
    name: string;
    surname: string;
    DIN: number;
    adders: string;
    city: string;
    emailP: string;
    password: string;
    Check: boolean;
  }

  export interface CreateUserDTO extends Omit<User, 'id'>{}

  export interface LoginModel{
    emailP: string;
    password: string;
  }
