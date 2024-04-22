export interface UserGet {
  firstName: string;
  lastName: string;
  email: string;
  password?: string;
  subscribe: string;
  subscription: {
    id_subscription: number;
    name: string;
    num_exp: number;
    num_secretary: number;
  };
  mercado_pago?: {
    status: string;
    init_point?: string;
    reason: string;
  };
}
