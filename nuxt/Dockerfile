# Use Node 20 Alpine image as the base
FROM node:20-alpine AS base

# Install required packages for node image
RUN apk --no-cache add openssh g++ make python3 git

# Create work directory for the app
WORKDIR /front-end

# Copy over package.json and package-lock.json
COPY package.json package-lock.json ./

# Install all dependencies
RUN npm ci && npm cache clean --force

# Copy over all project files to the work directory
COPY . .

# Build the project
RUN npm run build

# Final stage: production image
FROM node:20-alpine AS runner

WORKDIR /front-end

# Copy over build files from the builder stage
COPY --from=base /front-end/.output /front-end/.output

# Define environment variables
ENV NODE_ENV=production
ENV HOST=0.0.0.0
ENV PORT=3000
ENV VITE_API_BASE_URL=https://dev.deet.jp/api
ENV VITE_VAPID_PUBLIC_KEY='BPtbMQpHZFUntnwxmTbua_VxfULoJpKsrBFEsHrBh5nNAnq3W_uwlC5Gw8kl6RQA0CU7FqKaUeb4OerOGPIDoys'
ENV VITE_STRIPE_PUBLISHERKEY=pk_test_51QPoH3QAPkkuHM1C1OnmViNeQ0kHJHzQ8suoOD6iACvms4wDKFe08gsM2G78wds5OUufoSP27nvzlgDHkTmaaMlB00Ikklq5Wd

# Expose the host and port 3000
EXPOSE 3000

# Run the built project
CMD ["node", ".output/server/index.mjs"]
