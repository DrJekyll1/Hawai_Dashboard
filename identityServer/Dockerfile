FROM microsoft/aspnetcore-build:2.0 AS build-env
WORKDIR /app

# Copy csproj and restore as distinct layers
COPY *.csproj ./
RUN dotnet restore IdentityServer.csproj

# Copy everything else and build
COPY . ./
RUN dotnet publish -c Release -o out IdentityServer.csproj



# Build runtime image
FROM microsoft/aspnetcore:2.0
WORKDIR /app
COPY ./entrypoint.sh ./app/
RUN chmod +x ./app/entrypoint.sh
#CMD /bin/bash ./app/entrypoint.sh

COPY --from=build-env /app/out .
ENTRYPOINT ["dotnet", "IdentityServer.dll"]
# ENTRYPOINT ["/bin/bash", "./app/entrypoint.sh"]
# CMD ["dotnet", "IdentityServerWithAspIdAndEF.dll"]