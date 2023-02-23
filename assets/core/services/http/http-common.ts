import { env } from "../../../env";
import axios from "axios";

export default axios.create({
  baseURL: env.baseApiUrl,
  headers: {
    "Content-type": "application/json"
  }
});
