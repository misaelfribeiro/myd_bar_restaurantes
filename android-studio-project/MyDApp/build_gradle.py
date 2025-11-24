import urllib.request
import os
import subprocess
import sys
from pathlib import Path

# Define paths
project_dir = r"C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp"
wrapper_dir = os.path.join(project_dir, "gradle", "wrapper")
wrapper_jar = os.path.join(wrapper_dir, "gradle-wrapper.jar")
properties_file = os.path.join(wrapper_dir, "gradle-wrapper.properties")

print("[1] Reading gradle version from properties file...")
with open(properties_file, 'r') as f:
    content = f.read()
    # Find the distributionUrl
    for line in content.split('\n'):
        if 'distributionUrl' in line:
            gradle_url = line.split('=', 1)[1].strip().replace('\\', '')
            print(f"    Found URL: {gradle_url}")
            break

print(f"[2] Gradle wrapper JAR path: {wrapper_jar}")

if os.path.exists(wrapper_jar):
    print("    Gradle wrapper JAR already exists!")
else:
    print(f"    Downloading gradle-wrapper.jar...")
    wrapper_url = "https://services.gradle.org/distributions/gradle-8.13-wrapper.zip"
    try:
        urllib.request.urlretrieve(wrapper_url, os.path.join(wrapper_dir, "gradle-wrapper.zip"))
        print("    Downloaded!")
        
        # Extract the ZIP
        import zipfile
        with zipfile.ZipFile(os.path.join(wrapper_dir, "gradle-wrapper.zip"), 'r') as zip_ref:
            zip_ref.extractall(wrapper_dir)
        print("    Extracted!")
        
        # Clean up ZIP
        os.remove(os.path.join(wrapper_dir, "gradle-wrapper.zip"))
        print("    Cleaned up!")
    except Exception as e:
        print(f"    Error downloading: {e}")
        sys.exit(1)

print("[3] Now building with gradle...")
os.chdir(project_dir)
result = subprocess.run([sys.executable, "-m", "java", "-jar", wrapper_jar, "clean", "build"], capture_output=True, text=True)
print(result.stdout)
if result.stderr:
    print("STDERR:", result.stderr)
print(f"Return code: {result.returncode}")
