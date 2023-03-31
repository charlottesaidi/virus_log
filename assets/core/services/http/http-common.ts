import { env } from "../../../env";

interface HttpConfig {
  url: string;
}

export const config: HttpConfig = {
  url: env.baseApiUrl
}
